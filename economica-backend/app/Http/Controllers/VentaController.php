<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['usuario', 'detalles.producto'])
            ->latest()
            ->get();

        return response()->json($ventas, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $venta = Venta::create([
                'cliente' => $request->cliente ?? 'Consumidor Final',
                'user_id' => $request->user_id ?? $request->user()?->id ?? $this->defaultUserId(),
                'total' => 0,
            ]);

            $totalVenta = 0;

            foreach ($request->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                $cantidad = (int) $item['cantidad'];

                if ($producto->stock < $cantidad) {
                    DB::rollBack();

                    return response()->json([
                        'message' => "Stock insuficiente para el producto: {$producto->nombre}. Disponible: {$producto->stock}",
                    ], 400);
                }

                $subtotal = $producto->precio_venta * $cantidad;
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $cantidad);
            }

            $venta->update(['total' => $totalVenta]);

            DB::commit();

            return response()->json([
                'message' => 'Venta procesada con exito y stock actualizado',
                'data' => $venta->load('usuario', 'detalles.producto'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al procesar la venta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['usuario', 'detalles.producto'])->find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta, 200);
    }

    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'cliente' => 'nullable|string|max:100',
        ]);

        $venta->update([
            'cliente' => $request->cliente ?? $venta->cliente,
        ]);

        return response()->json([
            'message' => 'Venta actualizada correctamente',
            'data' => $venta->fresh('usuario', 'detalles.producto'),
        ]);
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();

        return response()->json([
            'message' => 'Venta eliminada correctamente',
        ]);
    }

    private function defaultUserId(): int
    {
        return User::firstOrCreate(
            ['email' => 'caja@economica.local'],
            [
                'name' => 'Caja',
                'password' => 'password',
            ]
        )->id;
    }
}
