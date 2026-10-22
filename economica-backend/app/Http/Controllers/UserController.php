<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::with('rol')
                ->orderBy('id', 'desc')
                ->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20|unique:users,telefono',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:rols,id',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => bcrypt($request->password),
            'rol_id' => $request->rol_id,
            'activo' => true
        ]);

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'data' => $usuario
        ], 201);
    }

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

    public function update(Request $request, $id)
    {
        $usuario = User::with('rol')->find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if ($this->esOtroAdministrador($usuario)) {
            return $this->accesoDenegado('No puedes editar a otro administrador.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'telefono' => 'nullable|string|max:20|unique:users,telefono,' . $id,
            'password' => 'nullable|string|min:8',
            'rol_id' => 'required|exists:rols,id',
            'activo' => 'required|boolean'
        ]);

        $data = [
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
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

    public function destroy($id)
    {
        $usuario = User::with('rol')->find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if ($this->esOtroAdministrador($usuario)) {
            return $this->accesoDenegado('No puedes eliminar a otro administrador.');
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    private function esOtroAdministrador(User $objetivo): bool
    {
        $actual = Auth::guard('api')->user();

        if ($actual && (int) $actual->id === (int) $objetivo->id) {
            return false;
        }

        $rol = strtolower(trim((string) ($objetivo->rol->nombre ?? '')));

        return str_contains($rol, 'admin');
    }

    private function accesoDenegado(string $mensaje)
    {
        return response()->json([
            'message' => $mensaje
        ], 403);
    }
}