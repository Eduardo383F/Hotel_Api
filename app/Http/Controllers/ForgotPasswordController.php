<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRules;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Envía el enlace de reseteo de contraseña.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Usamos el Broker de contraseñas de Laravel para enviar el enlace
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success('Enlace de recuperación enviado.', [], 200);
        }

        // Si falla, Laravel devuelve el motivo del error
        return ApiResponse::error($status, [], 422);
    }

    /**
     * Resetea la contraseña del usuario.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', PasswordRules::min(8)],
        ]);

        // Intentamos resetear la contraseña con el Broker
        $status = Password::reset(
            $request->all(),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success('Contraseña actualizada correctamente.', [], 200);
        }

        return ApiResponse::error($status, ['details' => $status], 422);
    }
}