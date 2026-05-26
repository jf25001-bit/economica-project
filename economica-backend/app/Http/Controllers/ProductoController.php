<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Trae el producto con su proveedor, su subcategoría y la categoría de esa subcategoría
        $productos = Producto::with(['subcategoria.categoria', 'proveedor'])->get();
        return response()->json($productos, 200);
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
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:100|unique:productos,nombre',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'sub_categoria_id' => 'required|exists:sub_categorias,id',
            'proveedor_id' => 'required|exists:proveedors,id', // Validando contra tu tabla 'proveedors'
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $datos = $request->all();

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
}

        $producto = Producto::create($datos);

        return response()->json([
            'message' => 'Producto creado con éxito',
            'data' => $producto
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::with(['subcategoria.categoria', 'proveedor'])->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $id,
            'nombre' => 'required|string|max:100|unique:productos,nombre,' . $id,
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'sub_categoria_id' => 'required|exists:sub_categorias,id',
            'proveedor_id' => 'required|exists:proveedors,id',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

                $datos = $request->all();

            if ($request->hasFile('imagen')) {

                // eliminar imagen anterior
                if ($producto->imagen) {
                    Storage::disk('public')->delete($producto->imagen);
                }

                // guardar nueva imagen
                $datos['imagen'] = $request->file('imagen')
                    ->store('productos', 'public');
            }

            $producto->update($datos);

        return response()->json([
            'message' => 'Producto actualizado con éxito',
            'data' => $producto
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        if ($producto->imagen) {
             Storage::disk('public')->delete($producto->imagen);
}

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado con éxito'], 200);
    }
}
