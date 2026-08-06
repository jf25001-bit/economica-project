<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Trae el producto con sus proveedores, subcategoría, imágenes y unidad de medida
        $productos = Producto::with([
            'subcategoria.categoria', 
            'proveedores', 
            'imagenes', 
            'unidadMedida'
        ])->get();

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
            'codigo_barras'    => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre'           => 'required|string|max:100|unique:productos,nombre',
            'precio_venta'     => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
            'sub_categoria_id' => 'nullable|required_without:categoria_id|exists:sub_categorias,id',
            'categoria_id'     => 'nullable|required_without:sub_categoria_id|exists:categorias,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medidas,id',
            // Validamos que 'proveedores' sea un array y que cada ID exista en la tabla proveedores
            'proveedores'      => 'nullable|array',
            'proveedores.*'    => 'exists:proveedores,id',
        ]);

        return DB::transaction(function () use ($request) {
            $data = $request->except('proveedores', 'categoria_id');
            $data['sub_categoria_id'] = $this->resolverSubcategoriaId($request);

            // Guardamos el producto (excluyendo el array de proveedores para no romper el create)
            $producto = Producto::create($data);

            // Sincronizamos los proveedores en la tabla pivote
            if ($request->has('proveedores')) {
                $producto->proveedores()->sync($request->proveedores);
            }

            // Retornamos el producto cargado con sus relaciones actualizadas
            return response()->json([
                'message' => 'Producto creado con éxito',
                'data'    => $producto->load('proveedores')
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::with([
            'subcategoria.categoria', 
            'proveedores', 
            'imagenes', 
            'unidadMedida'
        ])->find($id);

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
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->validate([
            'codigo_barras'    => 'nullable|string|max:50|unique:productos,codigo_barras,' . $id,
            'nombre'           => 'required|string|max:100|unique:productos,nombre,' . $id,
            'precio_venta'     => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
            'sub_categoria_id' => 'nullable|required_without:categoria_id|exists:sub_categorias,id',
            'categoria_id'     => 'nullable|required_without:sub_categoria_id|exists:categorias,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medidas,id',
            'proveedores'      => 'nullable|array',
            'proveedores.*'    => 'exists:proveedores,id',
        ]);

        return DB::transaction(function () use ($request, $producto) {
            $data = $request->except('proveedores', 'categoria_id');
            $data['sub_categoria_id'] = $this->resolverSubcategoriaId($request);

            // Actualizamos los datos del producto
            $producto->update($data);

            // Actualizamos la relación en la tabla pivote
            if ($request->has('proveedores')) {
                // sync() elimina los proveedores viejos y asigna la lista nueva
                $producto->proveedores()->sync($request->proveedores);
            }

            return response()->json([
                'message' => 'Producto actualizado con éxito',
                'data'    => $producto->load('proveedores')
            ], 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Al eliminar el producto, el onDelete('cascade') de la BD limpia automáticamente la tabla pivote
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado con éxito'], 200);
    }

    private function resolverSubcategoriaId(Request $request): int
    {
        if ($request->filled('sub_categoria_id')) {
            return (int) $request->sub_categoria_id;
        }

        $categoria = Categoria::findOrFail($request->categoria_id);
        $subcategoria = SubCategoria::firstOrCreate(
            [
                'categoria_id' => $categoria->id,
                'nombre' => $categoria->nombre,
            ]
        );

        return (int) $subcategoria->id;
    }
}
