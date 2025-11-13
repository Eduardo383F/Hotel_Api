<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Support\ApiResponse;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ContentPublicController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationActionsController;
use App\Http\Controllers\ReservationDocsController;
use App\Http\Controllers\RoomsPublicController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CartController;
/*
|--------------------------------------------------------------------------
| 1. Rutas Públicas (No requieren autenticación)
|--------------------------------------------------------------------------
|
| Endpoints para registro, login y contenido general del sitio web.
|
*/

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Contenido Web y Catálogo
Route::get('/availability', [AvailabilityController::class, 'index']);
Route::get('/rooms',        [RoomsPublicController::class, 'index']);
Route::get('/about',        [ContentPublicController::class, 'about']);
Route::get('/offers',       [ContentPublicController::class, 'offers']);
Route::get('/offers/{id}',  [ContentPublicController::class, 'offerShow']);
Route::get('/news',         [ContentPublicController::class, 'news']);
Route::get('/news/{id}',    [ContentPublicController::class, 'newsShow']);
Route::get('/events',       [ContentPublicController::class, 'events']);
Route::get('/events/{id}',  [ContentPublicController::class, 'eventShow']);
Route::get('/testimonials', [ContentPublicController::class, 'testimonials']);

// Endpoint de prueba
Route::get('/ping', fn() => response()->json(['message' => 'API funcionando']));

Route::middleware('auth:sanctum')->group(function () {
    
    // ... tus otras rutas ...

    // CARRITO DE COMPRAS
    Route::get('/cart', [CartController::class, 'index']);       // Ver carrito
    Route::post('/cart', [CartController::class, 'store']);      // Agregar habitación
    Route::delete('/cart/{id}', [CartController::class, 'destroy']); // Eliminar uno
    Route::delete('/cart', [CartController::class, 'clear']);    // Vaciar todo
});

/*
|--------------------------------------------------------------------------
| 2. Rutas Protegidas (Requieren autenticación con Sanctum)
|--------------------------------------------------------------------------
|
| Endpoints para clientes que ya han iniciado sesión.
|
*/

Route::middleware('auth:sanctum')->group(function () {


    // Solicitar enlace para resetear contraseña
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);

    // Resetear la contraseña con el token
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);

    // Flujo de Reservas
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations/{id}/pay', [PaymentController::class, 'payReservation']);
    Route::post('/reservations/{id}/checkout', [ReservationActionsController::class, 'checkout']);

    // Acciones y Documentos de Reserva
    Route::post('/reservations/{id}/send-confirmation', [ReservationActionsController::class, 'sendConfirmation']);
    Route::get('/reservations/{id}/voucher.pdf', [ReservationDocsController::class, 'voucher']);
    Route::get('/reservations/{id}/calendar.ics', [ReservationDocsController::class, 'calendar']);

    // Check-in (para personal del hotel, pero requiere token)
    Route::post('/checkin/scan', [ReservationActionsController::class, 'scanQr']);
    
    // Testimonios (el cliente debe estar logueado para enviar uno)
    Route::post('/testimonials', [TestimonialsController::class, 'store']);

    // Zona de Cliente (Ejemplo)
    Route::get('/zona-cliente', function (Request $request) {
        if ($request->user()->role !== 'cliente') {
            return ApiResponse::error('Acceso prohibido', [], 403);
        }
        return ApiResponse::success('Zona solo para clientes', [
            'id'   => $request->user()->id,
            'name' => $request->user()->name,
        ], 200);
    });
});