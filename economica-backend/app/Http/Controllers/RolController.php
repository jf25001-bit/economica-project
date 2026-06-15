<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    //lista todos los roles
    public function index(): JsonResponse
    {
        return response()->json(
            Rol::all(),
            200
        );
    }

    //crear un rol
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rols,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol = Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return response()->json([
            'message' => 'Rol creado con éxito',
            'data' => $rol
        ], 201);
    }

    //Muestra un rol
     
    public function show(Rol $rol): JsonResponse
    {
        return response()->json(
            $rol,
            200
        );
    }

    //actualiza datos del rol
    public function update(Request $request, Rol $rol): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rols,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return response()->json([
            'message' => 'Rol actualizado con éxito',
            'data' => $rol
        ], 200);
    }

   //elimina un rol
    public function destroy(Rol $rol): JsonResponse
    {
        $rol->delete();

        return response()->json([
            'message' => 'Rol eliminado con éxito'
        ], 200);
    }
}