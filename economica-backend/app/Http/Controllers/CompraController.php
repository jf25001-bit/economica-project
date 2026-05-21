<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // Mostrar compra

    public function index()
    {
         $compras = Compra::all();

         return response()->json($compras);
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
                'fecha_compra' => 'required|date',
                'total' => 'required|numeric',
                'proveedor_id' => 'required|exists:proveedors,id'
            ]);

            $compra = Compra::create($validated);

            return response()->json([
                'message' => 'Compra creada exitosamente',
                'data' => $compra
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

        $compra = Compra::findOrFail($id);
        return response()->json($compra);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Compra no encontrada'
        ], 404);
    }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Compra $compra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compra $compra)
    {
        try {
        $validated = $request->validate([
            'fecha_compra' => 'sometimes',
            'total' => 'sometimes|numeric',
            'proveedor_id' => 'sometimes'
        ]);

        $compra->update($validated);

        return response()->json([
            'message' => 'Compra actualizada',
            'data' => $compra
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Compra no encontrada'
        ], 404);
    }
    }

    /**
     * Remove the specified resource from storage.
     */

    //ESTE METODO NO SE UTILIZA, YA QUE LAS COMPRAS NO SE ELIMINAN, SOLO SE MARCAN COMO INACTIVAS
    
    public function destroy(compra $compra)
{
   
}
}
