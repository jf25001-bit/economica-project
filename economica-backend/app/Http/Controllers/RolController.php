<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $roles = Rol::all();
        return response()->json($roles, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        // Para API no usamos formulario; devolvemos respuesta vacía
        return response()->json(null, 204);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validamos los datos que vienen desde Vue
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rols,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Creamos el registro
        $rol = Rol::create($request->all());

        return response()->json([
            'message' => 'Rol creado con éxito',
            'data' => $rol
        ], 201); // 201: Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Rol $rol): JsonResponse
    {
        return response()->json($rol, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rol $rol): JsonResponse
    {
        // Para API no usamos formulario; devolvemos el recurso
        return response()->json($rol, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rol $rol): JsonResponse
    {
        // Validamos, ignorando el nombre del rol actual para que no choque con el 'unique'
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rols,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rol->update($request->all());

        return response()->json([
            'message' => 'Rol actualizado con éxito',
            'data' => $rol
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();

        return response()->json(['message' => 'Rol eliminado con éxito'], 200);
    }
}
