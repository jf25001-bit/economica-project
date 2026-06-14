<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
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
    



    protected function responseWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => JWTAuth::user(),
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

 public function me()
{
    return response()->json(
        User::with('rol')->find(JWTAuth::user()->id)
    );
}

    
    public function logout(){
        JWTAuth::logout();
        return response()->json([
            'message' => 'Sesión cerrada correctamente'
    ]);
    }

    
    public function refresh(){
        return $this->responseWithToken(JWTAuth::refresh());
    }


  
}
