<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomsPublicController extends Controller
{
    // GET /api/rooms
    public function index(Request $r)
    {
        // 1. Validación de filtros opcionales
        $r->validate([
            'room_type_id' => 'nullable|integer|exists:room_types,id',
            'adults'       => 'nullable|integer|min:1',
            'children'     => 'nullable|integer|min:0',
            'min_price'    => 'nullable|numeric|min:0',
            'max_price'    => 'nullable|numeric|min:0',
            'floor'        => 'nullable|integer',
        ]);

        $adults = (int)($r->adults ?? 1);
        $children = (int)($r->children ?? 0);

        // 2. Construcción de la consulta
        // Traemos TODAS las habitaciones, sin filtrar por estatus todavía
        $q = DB::table('rooms as rm')
            ->join('room_types as rt', 'rt.id', '=', 'rm.room_type_id')
            ->select(
                'rm.id as room_id',
                'rm.number as room_number',
                'rm.floor',
                'rm.status', // <--- Importante: traemos el estatus real de la BD
                'rt.id as room_type_id',
                'rt.name as room_type',
                'rt.base_price',
                'rt.max_adults',
                'rt.max_children'
            );

        // 3. Aplicar filtros si el usuario los envía
        if ($r->room_type_id) {
            $q->where('rm.room_type_id', $r->room_type_id);
        }
        if ($r->floor) {
            $q->where('rm.floor', $r->floor);
        }
        if ($r->min_price) {
            $q->where('rt.base_price', '>=', $r->min_price);
        }
        if ($r->max_price) {
            $q->where('rt.base_price', '<=', $r->max_price);
        }

        // Filtro de capacidad (opcional: solo mostrar las que caben, aunque estén ocupadas)
        $q->where(function ($x) use ($adults) {
            $x->whereNull('rt.max_adults')->orWhere('rt.max_adults', '>=', $adults);
        });
        $q->where(function ($x) use ($children) {
            $x->whereNull('rt.max_children')->orWhere('rt.max_children', '>=', $children);
        });

        $rows = $q->orderBy('rm.number')->get();

        // 4. Procesar cada habitación para asignar COLOR y Amenidades
        $items = $rows->map(function ($x) {
            
            // Lógica de Color: Disponible = verde, Cualquier otra cosa (ocupada/mtto) = rojo
            $isAvailable = ($x->status === 'disponible');
            $colorLabel = $isAvailable ? 'verde' : 'rojo';
            $colorHex   = $isAvailable ? '#28a745' : '#dc3545'; // Códigos Hex estándar (Bootstrap colors)

            // Traer amenidades (subconsulta rápida)
            $amenities = DB::table('room_feature as rf')
                ->join('features as f', 'f.id', '=', 'rf.feature_id')
                ->where('rf.room_id', $x->room_id)
                ->pluck('f.name')
                ->values();

            return [
                'room_id'         => (int)$x->room_id,
                'room_number'     => $x->room_number,
                'floor'           => $x->floor,
                'status'          => $x->status,       // Mostramos el texto real (disponible, ocupada...)
                'status_color'    => $colorLabel,      // "verde" o "rojo"
                'status_hex'      => $colorHex,        // El código de color para el CSS del front
                'room_type_id'    => (int)$x->room_type_id,
                'room_type'       => $x->room_type,
                'price_per_night' => (float)$x->base_price,
                'capacity'        => [
                    'max_adults'   => $x->max_adults,
                    'max_children' => $x->max_children,
                ],
                'amenities'       => $amenities,
                'currency'        => 'MXN',
            ];
        });

        return ApiResponse::success('Catálogo de habitaciones con estatus.', [
            'filters' => [
                'room_type_id' => $r->room_type_id,
                'floor'        => $r->floor,
            ],
            'items' => $items,
            'count' => $items->count(),
        ], 200);
    }
}