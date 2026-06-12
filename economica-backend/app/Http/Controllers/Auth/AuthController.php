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
    

    public function register(Request $request){
      //validamos datos a través de Request
      $validator = Validator::make($request->all(),[
          'name' => 'required|string|max:191',
        //   'email' => 'required|string|email|max:191|unique:users',
          'password' => 'required|string|min:8'
      ]);
      if($validator->fails()){
          return response()->json($validator->errors(),422);
      }
      //creamos el usuario
     $user = User::create([
    'name' => $request->name,
    'password' => Hash::make($request->password),
    'rol_id' => $request->rol_id ?? 1,
    'activo' => true
]);

      //Recordatorio--Asignar rol por defecto

      //generamos el token
      $token = JWTAuth::fromUser($user);
      //retornamos la respuesta

      return response()->json([
          'message' => 'Usuario registrado correctamente',
          'user' => $user,
          'access_token' => $token,
          'token_type' => 'bearer',
           'expires_in' => JWTAuth::factory()->getTTL() * 60
      ],201);
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

    //método para invalidar un token (logout)
    public function logout(){
        JWTAuth::logout();
        return response()->json([
            'message' => 'Sesión cerrada correctamente'
    ]);
    }

    //método para refrescar el token
    public function refresh(){
        return $this->responseWithToken(JWTAuth::refresh());
    }


  
}
