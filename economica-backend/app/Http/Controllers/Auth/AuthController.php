<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    // Verifica usuarios 
    public function login(Request $request)
    {
        $credenciales = $request->only('name','password');

        if (!$token = Auth::attempt($credenciales)) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::with('rol')->find(Auth::id());

        if (!$user->activo) {
            Auth::logout();
            return response()->json([
                'message' => 'Usuario desactivado'
            ], 403);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
    
    // Se crean usuarios nuevos
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id ?? 1,
            'activo' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }

    // Devuelve datos de la sesión
    protected function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => JWTAuth::user(),
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    // Muestra los datos del usuario actual
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            return response()->json(User::with('rol')->find($user->id));
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido o expirado'], 401);
        }
    }

    // Cierra la sesión del usuario de forma segura
    public function logout(Request $request)
    {
        try {
            // Obtener token directamente del encabezado o de la fachada
            $token = JWTAuth::getToken();
            
            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (JWTException $e) {
            // Si el token ya expiró o no se pudo invalidar, ignoramos la excepción
            // para permitir que el cliente cierre la sesión de todos modos
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }

    // Método para refrescar el token
    public function refresh()
    {
        try {
            return $this->responseWithToken(JWTAuth::refresh());
        } catch (JWTException $e) {
            return response()->json(['message' => 'No se pudo refrescar el token'], 401);
        }
    }
}