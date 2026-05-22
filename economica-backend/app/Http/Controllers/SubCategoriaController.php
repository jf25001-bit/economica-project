<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use Illuminate\Http\Request;

class SubCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // El 'with' carga la relación que definimos en el modelo
        $subcategorias = SubCategoria::with('categoria')->get();
        return response()->json($subcategorias, 200);
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
            'nombre' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id', // Valida que la categoría realmente exista en el sistema
        ]);

        $subcategoria = SubCategoria::create($request->all());

        return response()->json([
            'message' => 'Subcategoría creada con éxito',
            'data' => $subcategoria
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subcategoria = SubCategoria::with('categoria')->find($id);

        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        return response()->json($subcategoria, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategoria $subCategoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subcategoria = SubCategoria::find($id);

        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $subcategoria->update($request->all());

        return response()->json([
            'message' => 'Subcategoría actualizada con éxito',
            'data' => $subcategoria
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subcategoria = SubCategoria::find($id);

        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $subcategoria->delete();

        return response()->json(['message' => 'Subcategoría eliminada con éxito'], 200);
    }
}
