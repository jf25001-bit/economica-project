<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

//se listan  todos los usuarios
    public function index()
{
    return response()->json(
        User::with('rol')
            ->orderBy('id', 'desc')
            ->get(),
        200
    );
}
//aqui se crean usuarios 
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'rol_id' => 'required|exists:rols,id',
    ]);

    $usuario = User::create([
        'name' => $request->name,
        'apellido' => $request->apellido,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'rol_id' => $request->rol_id,
        'activo' => true
    ]);

    return response()->json([
        'message' => 'Usuario creado con éxito',
        'data' => $usuario
    ], 201);
}
//muestra un usuario
    public function show($id)
    {
        $usuario = User::with('rol')->find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($usuario);
    }
//actualiza los datos
    public function update(Request $request, $id)
{
    $usuario = User::find($id);

    if (!$usuario) {
        return response()->json([
            'message' => 'Usuario no encontrado'
        ], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'password' => 'nullable|string|min:8',
        'rol_id' => 'required|exists:rols,id',
        'activo' => 'required|boolean'
    ]);

    $data = [
        'name' => $request->name,
        'apellido' => $request->apellido,
        'email' => $request->email,
        'rol_id' => $request->rol_id,
        'activo' => $request->activo
    ];

    if ($request->filled('password')) {
        $data['password'] = bcrypt($request->password);
    }

    $usuario->update($data);

    return response()->json([
        'message' => 'Usuario actualizado',
        'data' => $usuario
    ]);
}
//eliminar un usuario
    public function destroy($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
