<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    private function esCajero(): bool
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return false;
        }

        return $usuario->rol?->nombre === 'Cajero';
    }

    public function index()
    {
        $productos = Producto::with([
            'subcategoria.categoria',
            'proveedores',
            'imagenes',
            'unidadMedida'
        ])->get();

        if ($this->esCajero()) {
            $productos->makeHidden(['precio_compra']);
        }

        return response()->json($productos, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
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
            $data = $request->except('proveedores', 'categoria_id');
            $data['sub_categoria_id'] = $this->resolverSubcategoriaId($request);

            $producto = Producto::create($data);

            if ($request->has('proveedores')) {
                $producto->proveedores()->sync($request->proveedores);
            }

            return response()->json([
                'message' => 'Producto creado con éxito',
                'data' => $producto->load('proveedores')
            ], 201);
        });
    }

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

        if ($this->esCajero()) {
            $producto->makeHidden(['precio_compra']);
        }

        return response()->json($producto, 200);
    }

    public function edit(Producto $producto)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

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
            $data = $request->except('proveedores', 'categoria_id');
            $data['sub_categoria_id'] = $this->resolverSubcategoriaId($request);

            $producto->update($data);

            if ($request->has('proveedores')) {
                $producto->proveedores()->sync($request->proveedores);
            }

            return response()->json([
                'message' => 'Producto actualizado con éxito',
                'data' => $producto->load('proveedores')
            ], 200);
        });
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

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

    private function resolverSubcategoriaId(Request $request): int
    {
        if ($request->filled('sub_categoria_id')) {
            return (int) $request->sub_categoria_id;
        }

        $categoria = Categoria::findOrFail($request->categoria_id);

        $subcategoria = SubCategoria::firstOrCreate([
            'categoria_id' => $categoria->id,
            'nombre' => $categoria->nombre,
        ]);

        return (int) $subcategoria->id;
    }
}