<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with('rol')->where('name', $fields['usuario'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Usuario o contrasena incorrectos.',
            ], 401);
        }

        if ($user->activo === false) {
            return response()->json([
                'message' => 'Este usuario esta desactivado.',
            ], 403);
        }

        return response()->json([
            'message' => 'Ingreso exitoso',
            'access_token' => 'mock-jwt-token-la-economica',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_id' => $user->rol_id,
                'rol' => $user->rol,
                'permisos' => $user->permisos ?? [],
                'activo' => $user->activo,
            ],
        ], 200);
    }
}
