<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\NewPasswordController;

/*
|--------------------------------------------------------------------------
| 1. Rutas Públicas (No requieren autenticación)
|--------------------------------------------------------------------------
|
| Endpoints para registro, login, contenido web y acciones seguras.
|
*/

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Recuperación de Contraseña (Públicas)
Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'reset']);

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

// Debug de Base de Datos (Opcional: Quitar en producción)
Route::get('/debug-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        $tableCount = DB::table('cart_items')->count();
        return response()->json([
            'status' => 'Conectado',
            'database_name' => $dbName,
            'cart_items_count' => $tableCount,
            'last_item' => DB::table('cart_items')->latest()->first()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

/*
|--------------------------------------------------------------------------
| 2. Rutas Especiales (Públicas pero Firmadas)
|--------------------------------------------------------------------------
|
| Permiten descargar archivos desde el correo sin iniciar sesión.
|
*/
Route::get('/reservations/{id}/download-pdf', [ReservationDocsController::class, 'downloadPdf'])
    ->name('reservations.pdf') 
    ->middleware('signed');

/*
|--------------------------------------------------------------------------
| 3. Rutas Protegidas (Requieren autenticación con Sanctum)
|--------------------------------------------------------------------------
|
| Endpoints para clientes que ya han iniciado sesión.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);

    // CARRITO DE COMPRAS
    Route::get('/cart', [CartController::class, 'index']);       // Ver carrito
    Route::post('/cart', [CartController::class, 'store']);      // Agregar habitación
    Route::delete('/cart/{id}', [CartController::class, 'destroy']); // Eliminar uno
    Route::delete('/cart', [CartController::class, 'clear']);    // Vaciar todo

    // Flujo de Reservas
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations/{id}/pay', [PaymentController::class, 'payReservation']);
    Route::post('/reservations/{id}/checkout', [ReservationActionsController::class, 'checkout']);

    // Acciones y Documentos de Reserva
    // (Nota: Quitamos la ruta duplicada que tenías antes)
    Route::post('/reservations/{id}/send-confirmation', [ReservationDocsController::class, 'sendConfirmation']);
    Route::get('/reservations/{id}/voucher.pdf', [ReservationDocsController::class, 'voucher']); // JSON para React
    Route::get('/reservations/{id}/calendar.ics', [ReservationDocsController::class, 'calendar']);
    
    // Check-in (para personal del hotel)
    Route::post('/checkin/scan', [ReservationActionsController::class, 'scanQr']); // Si no existe este controller, usa CheckinController
    
    // Testimonios
    Route::post('/testimonials', [TestimonialsController::class, 'store']);

    // Zona de Cliente
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