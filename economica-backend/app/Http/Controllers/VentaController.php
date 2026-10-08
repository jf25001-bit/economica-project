<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['usuario', 'detalles.producto'])->get();

        return response()->json($ventas, 200);
    }

    public function store(Request $request)
    {
        // No se puede vender si no hay una caja abierta
        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'message' => 'No se pueden realizar ventas: la caja está cerrada.'
            ], 403);
        }

        $request->validate([
            'fecha_venta' => 'nullable|date',
            'dinero_recibido' => 'required|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Usuario que emite la venta
        $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id');

        DB::beginTransaction();

        try {

            $venta = Venta::create([
                'user_id' => $userId ?? $request->user()?->id,
                'fecha_venta' => $request->fecha_venta ? \Carbon\Carbon::parse($request->fecha_venta)->setTimeFrom(now()) : now(),
                'cliente' => $request->input('cliente', 'Consumidor Final'),
                'total' => 0,
                'dinero_recibido' => $request->dinero_recibido,
                'vuelto' => 0,
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

                $cantidadSolicitada = (int) $item['cantidad'];
                $stockPorLotes = Lote::where('producto_id', $producto->id)
                    ->where('cantidad_actual', '>', 0)
                    ->sum('cantidad_actual');
                $stockDisponible = $stockPorLotes > 0
                    ? $stockPorLotes
                    : $producto->stock;

                if ($stockDisponible < $cantidadSolicitada) {

                    DB::rollBack();

                    return response()->json([
                        'message' => "Stock insuficiente para el producto: {$producto->nombre}. Disponible: {$stockDisponible}"
                    ], 400);
                }

                $subtotal = $producto->precio_venta * $cantidadSolicitada;
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidadSolicitada,
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal
                ]);

                if ($stockPorLotes > 0) {
                    $cantidadPendiente = $cantidadSolicitada;
                    $lotes = Lote::where('producto_id', $producto->id)
                        ->where('cantidad_actual', '>', 0)
                        ->orderByRaw('fecha_expiracion IS NULL, fecha_expiracion ASC')
                        ->orderBy('created_at')
                        ->lockForUpdate()
                        ->get();

                    foreach ($lotes as $lote) {
                        if ($cantidadPendiente <= 0) {
                            break;
                        }

                        $cantidadLote = min($lote->cantidad_actual, $cantidadPendiente);
                        $lote->decrement('cantidad_actual', $cantidadLote);
                        $cantidadPendiente -= $cantidadLote;
                    }
                }

                $producto->decrement('stock', $cantidadSolicitada);
            }

            if ($request->dinero_recibido < $totalVenta) {

                DB::rollBack();

                return response()->json([
                    'message' => "El dinero recibido (\${$request->dinero_recibido}) es menor al total de la venta (\${$totalVenta})"
                ], 400);
            }

            $vuelto = $request->dinero_recibido - $totalVenta;

            $venta->update([
                'total' => $totalVenta,
                'vuelto' => $vuelto
            ]);

            // Sumar la venta al total de la caja abierta
            $caja->increment('total_ventas', $totalVenta);

            DB::commit();

            return response()->json([
                'message' => 'Venta procesada con éxito',
                'data' => $venta->load(['usuario', 'detalles.producto'])
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
        $venta = Venta::with(['usuario', 'detalles.producto'])->find($id);

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
                'cliente' => 'sometimes|string|max:100',
                'dinero_recibido' => 'sometimes|numeric|min:0',
                'vuelto' => 'sometimes|numeric|min:0'
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