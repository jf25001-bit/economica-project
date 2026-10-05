<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompra;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DetalleCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(DetalleCompra::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'cantidad' => 'required|integer|min:1',
                'precio_compra' => 'required|numeric|min:0',
                'compra_id' => 'required|exists:compras,id',
                'producto_id' => 'required|exists:productos,id'
            ], [
                'cantidad.integer' => 'La cantidad debe ser un número entero (no se permiten medios paquetes).',
                'cantidad.min' => 'La cantidad mínima es 1.'
            ]);

            $detalle = DetalleCompra::create($validated);

            return response()->json([
                'message' => 'Detalle de compra creado',
                'data' => $detalle
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $detalle = DetalleCompra::findOrFail($id);

            return response()->json($detalle);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Detalle de compra no encontrado'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetalleCompra $detalleCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetalleCompra $detalleCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detalleCompra = DetalleCompra::findOrFail($id);

        $detalleCompra->delete();

        return response()->json(['message' => 'Detalle eliminado']);
    }
}