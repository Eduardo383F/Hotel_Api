<?php
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Mail\WelcomeEmail;
use App\Mail\ReservationConfirmation;
use App\Models\Reservation;

// 1. Vista Previa: Bienvenida
Route::get('/preview-welcome', function () {
    // Buscamos un usuario cualquiera para rellenar los datos
    $user = User::first() ?? User::factory()->make(); 
    return new WelcomeEmail($user);
});

// 2. Vista Previa: Confirmación de Reserva
Route::get('/preview-confirmation', function () {
    // Buscamos una reserva real con sus relaciones
    $reservation = Reservation::with(['user', 'room.type'])->first();
    
    if (!$reservation) {
        return "No hay reservas en la BD para probar.";
    }
    
    return new \App\Mail\ReservationConfirmation($reservation);
});