<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // <--- IMPORTANTE
use App\Mail\WelcomeEmail;           // <--- IMPORTANTE

class AuthController extends Controller
{
    // Registro de usuario (cliente)
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name'              => 'required|string|max:255',
            'email'    => 'required|string|email:rfc,dns|unique:users,email', 
            'password'          => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'name'      => $fields['name'],
            'email'     => strtolower($fields['email']),
            'password'  => bcrypt($fields['password']),
            'role'      => 'cliente', // explícito, aunque tu ENUM tenga default
        ]);

        // --- ENVIAR CORREO DE BIENVENIDA ---
        try {
            // Enviamos el correo a la dirección registrada
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            // Si falla el correo (por ejemplo, sin internet o malas credenciales SMTP),
            // lo registramos en el log pero NO detenemos el registro del usuario.
            \Illuminate\Support\Facades\Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
        }
        // -----------------------------------

        $token = $user->createToken('api_token')->plainTextToken;

        return ApiResponse::success('Usuario registrado correctamente y correo enviado.', [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'role'         => $user->role, 
            'access_token' => $token,
        ], 201);
    }

    // Login de usuario (cliente)
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower($fields['email']))->first();

        if (! $user || ! Hash::check($fields['password'], $user->password)) {
            return ApiResponse::error('Usuario no autorizado', [], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return ApiResponse::success('Usuario logueado exitosamente!', [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'role'         => $user->role,
            'access_token' => $token,
        ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success('Sesión cerrada correctamente.', [], 200);
    }
}