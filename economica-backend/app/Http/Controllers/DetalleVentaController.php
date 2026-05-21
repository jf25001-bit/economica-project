<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DetalleVentaController extends Controller
{
    // Mostrar todos los detalles
    public function index()
    {
        $detalles = DetalleVenta::all();
        return response()->json($detalles);
    }

    // Guardar detalle de venta
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'cantidad' => 'required|integer',
                'precio_venta' => 'required|numeric',
                'venta_id' => 'required',
                'producto_id' => 'required'
            ]);

            $detalle = DetalleVenta::create($validated);

            return response()->json([
                'message' => 'Detalle de venta creado correctamente',
                'data' => $detalle
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar un detalle específico
    public function show($id)
    {
        try {

            $detalle = DetalleVenta::findOrFail($id);

            return response()->json($detalle);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    // Actualizar detalle
    public function update(Request $request, $id)
    {
        try {

            $detalle = DetalleVenta::findOrFail($id);

            $validated = $request->validate([
                'cantidad' => 'sometimes|integer',
                'precio_venta' => 'sometimes|numeric',
                'venta_id' => 'sometimes',
                'producto_id' => 'sometimes'
            ]);

            $detalle->update($validated);

            return response()->json([
                'message' => 'Detalle actualizado correctamente',
                'data' => $detalle
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    // Eliminar detalle
    public function destroy($id)
    {
        try {

            $detalle = DetalleVenta::findOrFail($id);

            $detalle->delete();

            return response()->json([
                'message' => 'Detalle eliminado correctamente'
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }
}