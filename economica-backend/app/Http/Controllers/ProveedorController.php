<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = Proveedor::all();
        return response()->json($proveedores, 200);
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
        $request->validate([
            'nombre' => 'required|string|max:100|unique:proveedors,nombre',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor = Proveedor::create($request->all());

        return response()->json([
            'message' => 'Proveedor creado con éxito',
            'data' => $proveedor
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $proveedor = Proveedor::findOrFile($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        return response()->json($proveedor, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFile($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:100|unique:proveedors,nombre,' . $id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor->update($request->all());

        return response()->json([
            'message' => 'Proveedor actualizado con éxito',
            'data' => $proveedor
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFile($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $proveedor->delete();

        return response()->json(['message' => 'Proveedor eliminado con éxito'], 200);
    }
}
