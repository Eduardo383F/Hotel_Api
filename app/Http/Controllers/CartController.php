<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    // GET /api/cart - Ver el carrito del usuario
    public function index(Request $request)
    {
        $items = CartItem::with(['room.type']) // Traemos relaciones para mostrar precio y tipo
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($item) {
                // Calculamos totales al vuelo
                $nights = Carbon::parse($item->check_in)->diffInDays(Carbon::parse($item->check_out));
                $pricePerNight = $item->room->type->base_price;
                
                return [
                    'cart_item_id' => $item->id,
                    'room_id'      => $item->room_id,
                    'room_number'  => $item->room->number,
                    'room_type'    => $item->room->type->name,
                    'check_in'     => $item->check_in,
                    'check_out'    => $item->check_out,
                    'nights'       => $nights,
                    'adults'       => $item->adults,
                    'price_total'  => $nights * $pricePerNight,
                ];
            });

        return ApiResponse::success('Carrito de compras', [
            'items' => $items,
            'grand_total' => $items->sum('price_total'), // Suma total de todo el carrito
            'count' => $items->count()
        ]);
    }

    // POST /api/cart - Agregar al carrito
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults'    => 'required|integer|min:1',
            'children'  => 'nullable|integer|min:0',
        ]);

        $userId = $request->user()->id;

        // 1. Verificar Disponibilidad (Importante: no agregar si ya está ocupada)
        $isOccupied = DB::table('reservations')
            ->where('room_id', $data['room_id'])
            ->whereIn('status', ['tentativa', 'confirmada', 'completada'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('check_in', [$data['check_in'], $data['check_out']])
                      ->orWhereBetween('check_out', [$data['check_in'], $data['check_out']]);
            })->exists();

        if ($isOccupied) {
            return ApiResponse::error('La habitación no está disponible en esas fechas.', [], 409);
        }

        // 2. Agregar o Actualizar ítem en el carrito
        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => $userId,
                'room_id' => $data['room_id'] // Si ya tiene esa habitación, actualizamos fechas
            ],
            [
                'check_in'  => $data['check_in'],
                'check_out' => $data['check_out'],
                'adults'    => $data['adults'],
                'children'  => $data['children'] ?? 0,
            ]
        );

        return ApiResponse::success('Habitación agregada al carrito.', ['cart_item_id' => $cartItem->id], 201);
    }

    // DELETE /api/cart/{id} - Quitar del carrito
    public function destroy($id, Request $request)
    {
        $item = CartItem::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$item) {
            return ApiResponse::error('Ítem no encontrado', [], 404);
        }

        $item->delete();

        return ApiResponse::success('Ítem eliminado del carrito.');
    }
    
    // DELETE /api/cart - Vaciar carrito completo
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return ApiResponse::success('Carrito vaciado correctamente.');
    }
}