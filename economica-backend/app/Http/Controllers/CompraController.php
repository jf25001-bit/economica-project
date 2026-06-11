<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Compra::with([
                'detalles.producto.proveedor',
                'detalles.lotes',
            ]);

            if ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha_compra', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('fecha_compra', '<=', $request->fecha_fin);
            }

            $compras = $query->latest()->get();

            return response()->json($compras);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener compras',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_compra' => 'nullable|date',
            'fecha_llegada' => 'nullable|date',
            'estado' => 'nullable|in:pendiente,completada,cancelada',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_compra' => 'nullable|numeric|min:0',
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $estado = $request->estado ?? 'completada';

            $compra = Compra::create([
                'fecha_compra' => $request->fecha_compra ?? now(),
                'fecha_llegada' => $request->fecha_llegada,
                'estado' => $estado,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->detalles as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                $cantidad = (int) $item['cantidad'];
                $precio = $item['precio_compra'] ?? ($producto->precio_compra ?? 0);
                $subtotal = $cantidad * $precio;
                $total += $subtotal;

                $detalle = DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'subtotal' => $subtotal,
                ]);

                if ($estado === 'completada') {
                    Lote::create([
                        'detalle_compra_id' => $detalle->id,
                        'producto_id' => $producto->id,
                        'codigo_lote' => $item['codigo_lote'] ?? null,
                        'fecha_expiracion' => $item['fecha_expiracion'] ?? null,
                        'cantidad_inicial' => $cantidad,
                        'cantidad_actual' => $cantidad,
                    ]);

                    $producto->increment('stock', $cantidad);
                }
            }

            $compra->update(['total' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'Compra creada correctamente',
                'compra' => $compra->fresh('detalles.producto.proveedor', 'detalles.lotes'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear compra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $compra = Compra::with([
                'detalles.producto.proveedor',
                'detalles.lotes',
            ])->findOrFail($id);

            return response()->json($compra);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Compra no encontrada',
            ], 404);
        }
    }

    public function update(Request $request, Compra $compra)
    {
        $request->validate([
            'fecha_compra' => 'nullable|date',
            'fecha_llegada' => 'nullable|date',
            'estado' => 'nullable|in:pendiente,completada,cancelada',
            'detalles' => 'nullable|array',
            'detalles.*.detalle_id' => 'required|integer|exists:detalle_compras,id',
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $compra->load('detalles.producto', 'detalles.lotes');
            $estadoAnterior = $compra->estado;

            $compra->update([
                'fecha_compra' => $request->fecha_compra ?? $compra->fecha_compra,
                'fecha_llegada' => $request->fecha_llegada ?? $compra->fecha_llegada,
                'estado' => $request->estado ?? $compra->estado,
            ]);

            if ($request->has('detalles')) {
                foreach ($request->detalles as $input) {
                    $detalle = DetalleCompra::where('compra_id', $compra->id)
                        ->find($input['detalle_id']);

                    if (!$detalle) {
                        continue;
                    }

                    if ($estadoAnterior !== 'completada' && $compra->estado === 'completada') {
                        if (!$detalle->lotes()->exists()) {
                            Lote::create([
                                'detalle_compra_id' => $detalle->id,
                                'producto_id' => $detalle->producto_id,
                                'codigo_lote' => $input['codigo_lote'] ?? null,
                                'fecha_expiracion' => $input['fecha_expiracion'] ?? null,
                                'cantidad_inicial' => $detalle->cantidad,
                                'cantidad_actual' => $detalle->cantidad,
                            ]);

                            if ($detalle->producto) {
                                $detalle->producto->increment('stock', $detalle->cantidad);
                            }
                        }
                    } elseif ($compra->estado === 'completada') {
                        $lote = $detalle->lotes()->first();

                        if ($lote) {
                            $lote->update([
                                'codigo_lote' => $input['codigo_lote'] ?? $lote->codigo_lote,
                                'fecha_expiracion' => $input['fecha_expiracion'] ?? $lote->fecha_expiracion,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Compra actualizada correctamente',
                'data' => $compra->fresh('detalles.producto.proveedor', 'detalles.lotes'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al actualizar compra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Compra $compra)
    {
        $compra->delete();

        return response()->json([
            'message' => 'Compra eliminada correctamente',
        ]);
    }
}
