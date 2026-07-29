<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida; // Asegúrate de que tu modelo exista en app/Models/UnidadMedida.php
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $unidades = UnidadMedida::all();
            return response()->json($unidades, 200);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al obtener unidades de medida',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $unidad = UnidadMedida::create($request->all());

        return response()->json($unidad, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $unidad = UnidadMedida::find($id);

        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }

        return response()->json($unidad, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unidad = UnidadMedida::find($id);

        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }

        $unidad->update($request->all());

        return response()->json($unidad, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unidad = UnidadMedida::find($id);

        if (!$unidad) {
            return response()->json(['mensaje' => 'Unidad no encontrada'], 404);
        }

        $unidad->delete();

        return response()->json(['mensaje' => 'Unidad eliminada correctamente'], 200);
    }
}