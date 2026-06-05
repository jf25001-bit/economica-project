<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::with('rol')->get();
        return response()->json($usuarios, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|exists:rols,id',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol_id' => $request->rol_id,
        ]);

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'data' => $usuario
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = User::with('rol')->find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFile($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'rol_id' => 'required|exists:rols,id',
        ]);

        $data = $request->only(['name', 'email', 'rol_id']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuario actualizado con éxito',
            'data' => $usuario
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = User::findOrFile($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }
}
