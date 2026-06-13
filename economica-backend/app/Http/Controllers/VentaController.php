<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['detalles.producto'])->get();

        return response()->json($ventas, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            $venta = Venta::create([
                'fecha_venta' => now()->toDateString(),
                'cliente' => $request->input('cliente', 'Consumidor Final'),
                'total' => 0
            ]);

            $totalVenta = 0;

            foreach ($request->productos as $item) {

                $producto = Producto::find($item['producto_id']);

                if (!$producto) {

                    DB::rollBack();

                    return response()->json([
                        'message' => 'Producto no encontrado'
                    ], 404);
                }

                if ($producto->stock < $item['cantidad']) {

                    DB::rollBack();

                    return response()->json([
                        'message' => "Stock insuficiente para el producto: {$producto->nombre}. Disponible: {$producto->stock}"
                    ], 400);
                }

                $subtotal = $producto->precio_venta * $item['cantidad'];
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal
                ]);

                $producto->decrement('stock', $item['cantidad']);
            }

            $venta->update([
                'total' => $totalVenta
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Venta procesada con éxito',
                'data' => $venta->load('detalles.producto')
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Error al procesar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['detalles.producto'])->find($id);

        if (!$venta) {
            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }

        return response()->json($venta, 200);
    }

    public function update(Request $request, $id)
    {
        try {

            $venta = Venta::findOrFail($id);

            $validated = $request->validate([
                'fecha_venta' => 'sometimes|date',
                'total' => 'sometimes|numeric',
                'cliente' => 'sometimes|string|max:100'
            ]);

            $venta->update($validated);

            return response()->json([
                'message' => 'Venta actualizada correctamente',
                'data' => $venta
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    public function destroy($id)
    {
      //
    }
}