<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use Illuminate\Http\Request;

class SubCategoriaController extends Controller
{
    
    public function index()
    {
       
        $subcategorias = SubCategoria::with('categoria')->get();
        return response()->json($subcategorias, 200);
    }

    
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id', 
        ]);

        $subcategoria = SubCategoria::create($request->all());

        return response()->json([
            'message' => 'Subcategoría creada con éxito',
            'data' => $subcategoria
        ], 201);
    }

  
    public function show($id)
    {
        $subcategoria = SubCategoria::with('categoria')->find($id);

        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        return response()->json($subcategoria, 200);
    }

    
    public function edit(SubCategoria $subCategoria)
    {
        //
    }

   
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
