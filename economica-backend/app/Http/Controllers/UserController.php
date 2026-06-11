<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('rol');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'rol_id' => 'nullable|exists:rols,id',
            'activo' => 'nullable|boolean',
            'permisos' => 'nullable|array',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol_id' => $request->rol_id,
            'activo' => $request->boolean('activo', true),
            'permisos' => $request->permisos ?? [],
        ]);

        return response()->json([
            'message' => 'Usuario creado con exito',
            'data' => $usuario->load('rol'),
        ], 201);
    }

    public function show($id)
    {
        $usuario = User::with('rol')->find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario, 200);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'rol_id' => 'nullable|exists:rols,id',
            'activo' => 'nullable|boolean',
            'permisos' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'email', 'rol_id', 'activo', 'permisos']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuario actualizado con exito',
            'data' => $usuario->fresh('rol'),
        ], 200);
    }

    public function destroy($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->update(['activo' => false]);

        return response()->json(['message' => 'Usuario desactivado con exito'], 200);
    }
}
