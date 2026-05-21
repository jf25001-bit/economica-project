<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VentaController extends Controller
{
    // Mostrar todas las ventas
    public function index()
    {
        $ventas = Venta::all();
        return response()->json($ventas);
    }

    // Guardar venta
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'fecha_venta' => 'required|date',
                'total' => 'required|numeric',
                'cliente_id' => 'required'
            ]);

            $venta = Venta::create($validated);

            return response()->json([
                'message' => 'Venta creada correctamente',
                'data' => $venta
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar una venta
    public function show($id)
    {
        try {

            $venta = Venta::findOrFail($id);

            return response()->json($venta);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    // Actualizar venta
    public function update(Request $request, $id)
    {
        try {

            $venta = Venta::findOrFail($id);

            $validated = $request->validate([
                'fecha_venta' => 'sometimes|date',
                'total' => 'sometimes|numeric',
                'cliente_id' => 'sometimes'
            ]);

            $venta->update($validated);

            return response()->json([
                'message' => 'Venta actualizada correctamente',
                'data' => $venta
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    // Eliminar venta
    public function destroy($id)
    {
        try {

            $venta = Venta::findOrFail($id);

            $venta->delete();

            return response()->json([
                'message' => 'Venta eliminada correctamente'
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }
}