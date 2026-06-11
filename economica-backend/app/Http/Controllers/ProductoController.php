<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        // 1. Inicializamos la consulta
        $query = Producto::with(['categoria', 'subcategoria', 'proveedor']);

        // 2. Filtros de búsqueda
        if ($request->search) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        if ($request->categoria) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->sub_categoria) {
            $query->where('sub_categoria_id', $request->sub_categoria);
        }

        if ($request->estado === 'bajo_stock') {
            $query->whereRaw('stock <= stock_minimo');
        }

        if ($request->estado === 'disponible') {
            $query->whereRaw('stock > stock_minimo');
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // 3. Lógica de ordenamiento (Movida adentro de index antes del return)
        switch ($request->ordenar) {
            case 'nombre':
                $query->orderBy('nombre');
                break;

            case 'precio':
                $query->orderBy('precio_venta');
                break;

            default:
                $query->latest(); // Esto es lo mismo que ->orderBy('created_at', 'desc')
                break;
        }

        // 4. Ejecutar la consulta y retornar la paginación
        $perPage = min((int) $request->input('per_page', 10), 1000);

        return $query->paginate($perPage);
    }

    public function resumen()
    {
        return response()->json([
            'total' => Producto::count(),

            'disponibles' => Producto::whereColumn('stock', '>', 'stock_minimo')
                ->count(),

            'bajo_stock' => Producto::whereColumn('stock', '<=', 'stock_minimo')
                ->count(),
        ]);
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
        $request->merge([
            'sub_categoria_id' => $request->filled('sub_categoria_id')
                ? $request->sub_categoria_id
                : null,
        ]);

        $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:100|unique:productos,nombre',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'sub_categoria_id' => 'nullable|exists:sub_categorias,id',
            'proveedor_id' => 'required|exists:proveedors,id', // Validando contra tu tabla 'proveedors'
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $datos = $request->all();

        if ($datos['sub_categoria_id']) {
            $subcategoria = SubCategoria::find($datos['sub_categoria_id']);

            if ((int) $subcategoria->categoria_id !== (int) $datos['categoria_id']) {
                return response()->json([
                    'message' => 'La subcategoría no pertenece a la categoría seleccionada'
                ], 422);
            }
        }

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
        $producto = Producto::with(['categoria', 'subcategoria.categoria', 'proveedor'])->find($id);

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
        $producto = Producto::findOrFail($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->merge([
            'sub_categoria_id' => $request->filled('sub_categoria_id')
                ? $request->sub_categoria_id
                : null,
        ]);

        $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $id,
            'nombre' => 'required|string|max:100|unique:productos,nombre,' . $id,
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'sub_categoria_id' => 'nullable|exists:sub_categorias,id',
            'proveedor_id' => 'required|exists:proveedors,id',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

            $datos = $request->all();

            if ($datos['sub_categoria_id']) {
                $subcategoria = SubCategoria::find($datos['sub_categoria_id']);

                if ((int) $subcategoria->categoria_id !== (int) $datos['categoria_id']) {
                    return response()->json([
                        'message' => 'La subcategoría no pertenece a la categoría seleccionada'
                    ], 422);
                }
            }

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
        $producto = Producto::find($id);
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
