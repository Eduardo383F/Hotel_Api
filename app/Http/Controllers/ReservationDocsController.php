<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationDocsController extends Controller
{
    /**
     * GET /api/reservations/{id}/vouch
     * Devuelve JSON con los datos para que React dibuje el Boleto y el QR.
     */
    public function voucher($id, Request $request)
    {
        // 1. Buscar la reserva con sus relaciones
        // NOTA: Eliminamos 'u.phone_number' porque no existe en tu tabla 'users'
        $res = DB::table('reservations as r')
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->join('rooms as rm', 'r.room_id', '=', 'rm.id')
            ->join('room_types as rt', 'rm.room_type_id', '=', 'rt.id')
            ->select(
                'r.*',
                'u.name as user_name',
                'u.email as user_email',
                'rm.number as room_number',
                'rt.name as room_type',
                'rt.base_price as night_price'
            )
            ->where('r.id', $id)
            ->first();

        // 2. Validaciones de Seguridad
        if (!$res) {
            return ApiResponse::error('Reserva no encontrada', [], 404);
        }

        // Solo el dueño de la reserva (o un admin) puede ver el voucher
        if ($request->user()->id !== (int)$res->user_id) {
            return ApiResponse::error('No autorizado para ver este comprobante', [], 403);
        }

        // 3. Calcular Extras
        $extrasTotal = DB::table('reservation_extra')
            ->where('reservation_id', $id)
            ->sum('total_price');

        // 4. Buscar información del pago
        $payment = DB::table('payments')
            ->where('reservation_id', $id)
            ->where('status', 'pagado')
            ->latest()
            ->first();

        // 5. Calcular noches
        $checkIn = Carbon::parse($res->check_in);
        $checkOut = Carbon::parse($res->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Evitar división por cero o noches negativas
        $nights = $nights < 1 ? 1 : $nights;

        // 6. Armar la respuesta JSON
        $data = [
            'folio' => 'RES-' . str_pad($res->id, 6, '0', STR_PAD_LEFT),
            'reservation_id' => $res->id,
            'status' => ucfirst($res->status),
            'created_at' => $res->created_at,
            'guest' => [
                'name' => $res->user_name,
                'email' => $res->user_email,
                'phone' => 'N/A', // Valor por defecto
            ],
            'room' => [
                'number' => $res->room_number,
                'type_name' => $res->room_type,
                'night_price' => (float) $res->night_price,
            ],
            'stay' => [
                'check_in' => $res->check_in,
                'check_out' => $res->check_out,
                'nights' => $nights,
                'adults' => $res->adults,
                'children' => $res->children,
            ],
            'financials' => [
                'total_room' => (float) $res->total_price,
                'total_extras' => (float) $extrasTotal,
                'grand_total' => (float) ($res->total_price + $extrasTotal),
                'payment_method' => $payment ? ucfirst($payment->payment_method) : 'Pendiente',
            ],
            // STRING PARA EL QR: Usamos el token de la BD o generamos uno seguro
            'qr_code_string' => $res->qr_token ?? ('RES-' . $res->id . '-' . md5($res->created_at . $res->user_id)),
        ];

        return ApiResponse::success('Datos del voucher recuperados', $data);
    }

    /**
     * GET /api/reservations/{id}/calendar.ics
     * Este sigue devolviendo el archivo de calendario.
     */
    public function calendar($id, Request $r)
    {
        $res = DB::table('reservations')->where('id', $id)->first();
        
        if (!$res || $r->user()->id !== (int)$res->user_id) {
            abort(404);
        }

        $uid   = "res-{$id}@hotel.local";
        $start = Carbon::parse($res->check_in)->format('Ymd');
        $end   = Carbon::parse($res->check_out)->format('Ymd');
        
        $ics = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Hotel//Booking//ES
BEGIN:VEVENT
UID:{$uid}
DTSTAMP:".now()->utc()->format('Ymd\THis\Z')."
DTSTART;VALUE=DATE:{$start}
DTEND;VALUE=DATE:{$end}
SUMMARY:Estancia en Hotel - Habitación {$res->room_id}
DESCRIPTION:Reserva confirmada.
END:VEVENT
END:VCALENDAR";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"reserva-{$id}.ics\"",
        ]);
    }
}