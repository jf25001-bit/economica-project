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
    public function index()
    {
        try {
            $compras = Compra::with([
                'detalles.producto',
                'detalles.lotes'
            ])->latest()->get();
            
            return response()->json($compras);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener compras',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_compra' => 'nullable|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1', 
            'detalles.*.unidades_por_paquete' => 'nullable|integer|min:1', 
            'detalles.*.precio_compra' => 'required|numeric|min:0', 
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            $compra = Compra::create([
                'fecha_compra' => $request->fecha_compra ?? now(),
                'total' => 0
            ]);

            $totalGeneral = 0;

            foreach ($request->detalles as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $paquetesComprados = (int) $item['cantidad'];
                $unidadesPorPaquete = isset($item['unidades_por_paquete']) && $item['unidades_por_paquete'] > 0 
                    ? (int) $item['unidades_por_paquete'] 
                    : 1;

                $unidadesTotales = $paquetesComprados * $unidadesPorPaquete;
                $precioPaquete = (float) $item['precio_compra'];
                $subtotal = $paquetesComprados * $precioPaquete;
                $totalGeneral += $subtotal;

                $precioUnitarioCalculado = $precioPaquete / $unidadesPorPaquete;

                $detalle = DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $unidadesTotales,
                    'unidades_por_paquete' => $unidadesPorPaquete,
                    'precio_compra' => $precioUnitarioCalculado,
                    'subtotal' => $subtotal
                ]);

                $codigoLote = !empty($item['codigo_lote']) 
                    ? $item['codigo_lote'] 
                    : 'LOTE-C' . $compra->id . '-P' . $producto->id;

                Lote::create([
                    'detalle_compra_id' => $detalle->id,
                    'producto_id'       => $producto->id,
                    'codigo_lote'       => $codigoLote,
                    'fecha_expiracion'  => $item['fecha_expiracion'] ?? null,
                    'cantidad_inicial'  => $unidadesTotales,
                    'cantidad_actual'   => $unidadesTotales
                ]);

                $producto->increment('stock', $unidadesTotales);
            }

            $compra->update(['total' => $totalGeneral]);

            DB::commit();

            return response()->json([
                'message' => 'Compra procesada e inventario actualizado correctamente',
                'compra' => $compra->fresh('detalles.producto', 'detalles.lotes')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_compra' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.unidades_por_paquete' => 'required|integer|min:1',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date'
        ]);

        return DB::transaction(function () use ($request, $id) {
            $compra = Compra::with('detalles.lotes')->findOrFail($id);
            
            $totalGeneral = 0;
            $detallesRecibidos = collect($request->detalles);
            $idsEnviados = $detallesRecibidos->pluck('detalle_id')->filter()->toArray();

            // 1. Revertir el stock de detalles eliminados
            $detallesAEliminar = $compra->detalles()->whereNotIn('id', $idsEnviados)->get();
            foreach ($detallesAEliminar as $detOld) {
                Producto::where('id', $detOld->producto_id)->decrement('stock', $detOld->cantidad);
                $detOld->delete();
            }

            // 2. Procesar ítems nuevos o actualizados
            foreach ($detallesRecibidos as $det) {
                $paquetes = (int) $det['cantidad'];
                $unidPorPaquete = (int) $det['unidades_por_paquete'];
                $unidadesTotalesNuevas = $paquetes * $unidPorPaquete;
                $precioPaquete = (float) $det['precio_compra'];
                $subtotalItem = $paquetes * $precioPaquete;
                $precioUnitario = $precioPaquete / $unidPorPaquete;

                $totalGeneral += $subtotalItem;

                $detalleExistente = isset($det['detalle_id']) ? DetalleCompra::find($det['detalle_id']) : null;

                if ($detalleExistente) {
                    // Ajuste de stock por la diferencia
                    $diferenciaStock = $unidadesTotalesNuevas - $detalleExistente->cantidad;
                    if ($diferenciaStock != 0) {
                        Producto::where('id', $det['producto_id'])->increment('stock', $diferenciaStock);
                    }

                    $detalleExistente->update([
                        'producto_id' => $det['producto_id'],
                        'cantidad' => $unidadesTotalesNuevas,
                        'unidades_por_paquete' => $unidPorPaquete,
                        'precio_compra' => $precioUnitario,
                        'subtotal' => $subtotalItem
                    ]);

                    $detalle = $detalleExistente;
                } else {
                    // Detalle nuevo agregado durante la edición
                    $detalle = $compra->detalles()->create([
                        'producto_id' => $det['producto_id'],
                        'cantidad' => $unidadesTotalesNuevas,
                        'unidades_por_paquete' => $unidPorPaquete,
                        'precio_compra' => $precioUnitario,
                        'subtotal' => $subtotalItem
                    ]);

                    Producto::where('id', $det['producto_id'])->increment('stock', $unidadesTotalesNuevas);
                }

                // Actualizar o Crear el Lote
                $codigoLote = !empty($det['codigo_lote']) 
                    ? $det['codigo_lote'] 
                    : 'LOTE-C' . $compra->id . '-P' . $det['producto_id'];

                Lote::updateOrCreate(
                    ['detalle_compra_id' => $detalle->id],
                    [
                        'producto_id' => $det['producto_id'],
                        'codigo_lote' => $codigoLote,
                        'fecha_expiracion' => $det['fecha_expiracion'] ?? null,
                        'cantidad_inicial' => $unidadesTotalesNuevas,
                        'cantidad_actual' => $unidadesTotalesNuevas
                    ]
                );
            }

            // 3. Actualizar la compra con la nueva fecha y total recalculado
            $compra->update([
                'fecha_compra' => $request->fecha_compra,
                'total' => $totalGeneral,
            ]);

            return response()->json([
                'message' => 'Orden de compra actualizada con éxito',
                'compra' => $compra->fresh('detalles.producto', 'detalles.lotes')
            ], 200);
        });
    }

    public function show($id)
    {
        try {
            $compra = Compra::with([
                'detalles.producto',
                'detalles.lotes'
            ])->findOrFail($id);
            return response()->json($compra);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Compra no encontrada'
            ], 404);
        }
    }
}