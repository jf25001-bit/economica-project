<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::with('subcategorias')->get();
        return response()->json($categorias, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria = Categoria::create($request->all());

        return response()->json([
            'message' => 'Categoría creada con éxito',
            'data' => $categoria
        ], 201);
    }

    public function show($id)
    {
        $categoria = Categoria::with('subcategorias')->find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json($categoria, 200);
    }

    public function edit(Categoria $categoria)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria->update($request->all());

        return response()->json([
            'message' => 'Categoría actualizada con éxito',
            'data' => $categoria
        ], 200);
    }

    public function destroy($id)
    {
        $categoria = Categoria::with('subcategorias.productos')->find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        // Verificar si la categoría tiene productos afiliados
        foreach ($categoria->subcategorias as $subcategoria) {
            if ($subcategoria->productos()->count() > 0) {
                return response()->json([
                    'message' => "No se puede eliminar la categoría \"{$categoria->nombre}\" porque tiene subcategorías con productos afiliados."
                ], 422);
            }
        }

        $categoria->delete();

        return response()->json(['message' => 'Categoría eliminada con éxito'], 200);
    }
}