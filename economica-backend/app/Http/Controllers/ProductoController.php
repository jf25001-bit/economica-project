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
     * Verifica si el usuario autenticado es Cajero.
     */
    private function esCajero(): bool
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return false;
        }

        return $usuario->rol?->nombre === 'Cajero';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with([
            'subcategoria.categoria',
            'proveedores',
            'imagenes',
            'unidadMedida'
        ])->get();

        /*
         * Si es Cajero, ocultamos precio_venta de la respuesta.
         */
        if ($this->esCajero()) {
            $productos->makeHidden(['precio_venta']);
        }

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
        /*
         * Si es Cajero, NO puede crear productos con precio.
         */
        if ($this->esCajero()) {
            return response()->json([
                'message' => 'Los usuarios con rol Cajero no tienen permiso para crear productos.'
            ], 403);
        }

        $request->validate([
            'codigo_barras'    => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre'           => 'required|string|max:100|unique:productos,nombre',
            'precio_venta'     => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'stock_minimo'     => 'required|integer|min:0',
            'sub_categoria_id' => 'nullable|required_without:categoria_id|exists:sub_categorias,id',
            'categoria_id'     => 'nullable|required_without:sub_categoria_id|exists:categorias,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medidas,id',
            'proveedores'      => 'nullable|array',
            'proveedores.*'    => 'exists:proveedores,id',
        ]);

        return DB::transaction(function () use ($request) {

            $data = $request->except(
                'proveedores',
                'categoria_id'
            );

            $data['sub_categoria_id'] =
                $this->resolverSubcategoriaId($request);

            $producto = Producto::create($data);

            if ($request->has('proveedores')) {
                $producto->proveedores()->sync(
                    $request->proveedores
                );
            }

            return response()->json([
                'message' => 'Producto creado con éxito',
                'data' => $producto->load('proveedores')
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
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        /*
         * Si es Cajero, ocultamos el precio.
         */
        if ($this->esCajero()) {
            $producto->makeHidden([
                'precio_venta'
            ]);
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
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        /*
         * Si es Cajero, no puede modificar productos.
         */
        if ($this->esCajero()) {
            return response()->json([
                'message' => 'Los usuarios con rol Cajero no tienen permiso para modificar productos.'
            ], 403);
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

            $data = $request->except(
                'proveedores',
                'categoria_id'
            );

            $data['sub_categoria_id'] =
                $this->resolverSubcategoriaId($request);

            $producto->update($data);

            if ($request->has('proveedores')) {
                $producto->proveedores()->sync(
                    $request->proveedores
                );
            }

            return response()->json([
                'message' => 'Producto actualizado con éxito',
                'data' => $producto->load('proveedores')
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
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        /*
         * Si es Cajero, no puede eliminar productos.
         */
        if ($this->esCajero()) {
            return response()->json([
                'message' => 'Los usuarios con rol Cajero no tienen permiso para eliminar productos.'
            ], 403);
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado con éxito'
        ], 200);
    }

    /**
     * Resolver la subcategoría.
     */
    private function resolverSubcategoriaId(Request $request): int
    {
        if ($request->filled('sub_categoria_id')) {
            return (int) $request->sub_categoria_id;
        }

        $categoria = Categoria::findOrFail(
            $request->categoria_id
        );

        $subcategoria = SubCategoria::firstOrCreate(
            [
                'categoria_id' => $categoria->id,
                'nombre' => $categoria->nombre,
            ]
        );

        return (int) $subcategoria->id;
    }
}