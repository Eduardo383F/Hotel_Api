<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // <--- Importamos Mail
use App\Mail\ReservationConfirmation; // <--- Importamos tu correo nuevo
use App\Models\Reservation;           // <--- Importamos el Modelo
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;

class PaymentController extends Controller
{
    public function payReservation($id, Request $r)
    {
        // 1. Validación de entrada
        $fields = $r->validate([
            'payment_method_id' => 'required|string',
            'idempotency_key' => 'required|string|max:64',
        ]);

        // 2. Traer reserva básica
        $res = DB::table('reservations')->where('id', $id)->first();

        if (!$res) {
            return ApiResponse::error('Recurso no encontrado', [], 404);
        }
        if ($r->user()->id !== (int) $res->user_id) {
            return ApiResponse::error('No autorizado para esta reserva', ['code' => 'FORBIDDEN'], 403);
        }
        if ($res->status !== 'tentativa') {
            return ApiResponse::error('La reserva no está en estado tentativa', ['code' => 'INVALID_STATUS'], 409);
        }

        // 3. Calcular monto final
        $roomTotal = (float) $res->total_price;
        $extrasTotal = (float) DB::table('reservation_extra')
            ->where('reservation_id', $res->id)
            ->sum('total_price');
        $amount = $roomTotal + $extrasTotal;

        // --- INICIA LÓGICA DE STRIPE ---
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
           
            // 4. Crear el intento de pago en Stripe
            $paymentIntent = PaymentIntent::create(
                [
                    'amount' => $amount * 100, // Centavos
                    'currency' => 'mxn',
                    'payment_method' => $fields['payment_method_id'],
                    'confirm' => true,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'metadata' => [
                        'reservation_id' => $res->id,
                        'user_id' => $r->user()->id,
                    ]
                ],
                ['idempotency_key' => $fields['idempotency_key']]
            );

            // --- FIN LÓGICA DE STRIPE ---

            // Si el pago es exitoso, procedemos
            DB::beginTransaction();

            // 5. Insertar el registro del pago
            $paymentId = DB::table('payments')->insertGetId([
                'reservation_id' => $res->id,
                'amount' => $amount,
                'payment_method' => 'tarjeta',
                'payment_date' => now(),
                'status' => 'pagado',
                'reference' => $fields['idempotency_key'],
                'gateway_provider' => 'stripe',
                'gateway_intent_id' => $paymentIntent->id,
                'gateway_charge_id' => $paymentIntent->latest_charge,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Actualizar la reserva a "confirmada"
            $colorId = DB::table('color_codes')->where('color_name', 'Amarillo')->value('id');
            
            DB::table('reservations')->where('id', $res->id)->update([
                'status' => 'confirmada',
                'color_code_id' => $colorId ?: null,
                'updated_at' => now(),
            ]);
            
            // Opcional: También bloquear la habitación en 'rooms' si tu lógica lo requiere
            // DB::table('rooms')->where('id', $res->room_id)->update(['status' => 'ocupada']);

            DB::commit();

            // 7. ENVIAR CONFIRMACIÓN + QR AUTOMÁTICAMENTE 📧
            $emailSent = false;
            try {
                // Truco: Recargamos la reserva como Modelo Eloquent para tener las relaciones (user, room)
                $reservationModel = Reservation::with(['user', 'room.type'])->find($res->id);
                
                if ($reservationModel && $reservationModel->user) {
                    Mail::to($reservationModel->user->email)->send(new ReservationConfirmation($reservationModel));
                    $emailSent = true;
                }
            } catch (\Throwable $e) {
                // Si falla el correo, lo registramos en el log pero NO fallamos el pago
                \Illuminate\Support\Facades\Log::error("Error enviando correo de confirmación: " . $e->getMessage());
                $emailSent = false;
            }

            // 8. Respuesta final
            return ApiResponse::success('Pago aplicado y reserva confirmada.', [
                'reservation_id' => (int) $res->id,
                'status' => 'confirmada',
                'payment' => [
                    'payment_id' => (int) $paymentId,
                    'status' => 'pagado',
                    'amount' => $amount,
                    'gateway_ref' => $paymentIntent->id,
                ],
                'email_sent' => $emailSent,
            ], 200);

        } catch (CardException $e) {
            // Pago rechazado por el banco
            return ApiResponse::error('Pago rechazado por el banco', ['code' => 'CARD_DECLINED', 'details' => $e->getError()->message], 402);
        } catch (\Throwable $e) {
            // Cualquier otro error
            DB::rollBack();
            return ApiResponse::error('Error interno al aplicar el pago', ['exception' => class_basename($e), 'message' => $e->getMessage()], 500);
        }
    }
}