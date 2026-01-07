<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Paso 1: El usuario pide el enlace (POST /api/forgot-password)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Laravel intenta enviar el link usando la notificación que configuramos en User.php
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success(__($status)); // "Recordatorio de contraseña enviado!"
        }

        return ApiResponse::error(__($status), [], 400);
    }

    /**
     * Paso 2: El usuario envía la nueva contraseña (POST /api/reset-password)
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success(__($status)); // "¡Tu contraseña ha sido restablecida!"
        }

        return ApiResponse::error(__($status), [], 400);
    }
}