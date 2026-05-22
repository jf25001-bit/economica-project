<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['proveedor', 'usuario', 'detalles.producto'])->get();
        return response()->json($compras, 200);
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
            'proveedor_id' => 'required|exists:proveedors,id', // Apunta a tu tabla real
            'user_id' => 'required|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear cabecera de la compra
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'user_id' => $request->user_id,
                'total' => 0
            ]);

            $totalCompra = 0;

            // 2. Procesar los productos agregados
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);

                // Usamos el precio de compra registrado en el producto
                $subtotal = $producto->precio_compra * $item['cantidad'];
                $totalCompra += $subtotal;

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_compra' => $producto->precio_compra,
                    'subtotal' => $subtotal
                ]);

                // ¡Efecto Inverso!: Incrementamos el stock del inventario
                $producto->increment('stock', $item['cantidad']);
            }

            // 3. Guardar el total definitivo de la compra
            $compra->update(['total' => $totalCompra]);

            DB::commit();

            return response()->json([
                'message' => 'Compra registrada con éxito y stock reabastecido (+)',
                'data' => $compra->load('detalles')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'usuario', 'detalles.producto'])->find($id);

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        return response()->json($compra, 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        //
    }
}
