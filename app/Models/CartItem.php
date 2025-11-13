<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'adults',
        'children',
    ];

    // Relación para traer los datos de la habitación (precio, numero, tipo)
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}