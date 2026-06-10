<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Dejamos la validación del request tal como está en el backend 'usuario'
        $fields = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);

        // 2. MODIFICACIÓN AQUÍ: Cambiamos 'usuario' por 'name' para que busque en la columna real de MySQL
        $user = User::where('name', $fields['usuario'])->first();

        // 3. Evaluamos si el registro existe y la contraseña encriptada coincide
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Usuario o contraseña incorrectos.'
            ], 401);
        }

        // 4. Autenticación exitosa: Generamos y retornamos la respuesta limpia en JSON
        return response()->json([
            'message' => '¡Ingreso exitoso!',
            'access_token' => 'mock-jwt-token-la-economica', // Si usas Sanctum cámbialo por $user->createToken('token')->plainTextToken
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'rol_id' => $user->rol_id ?? 1
            ]
        ], 200);
    }
}
