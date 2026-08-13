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
        // Se crea la compra general
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

            // Creación del detalle de la compra
            $detalle = DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $unidadesTotales,
                'precio_compra' => $precioUnitarioCalculado,
                'subtotal' => $subtotal
            ]);

            // Asignación de lote (si viene vacío, se genera un código dinámico)
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

            // Actualización inmediata de stock
            $producto->increment('stock', $unidadesTotales);
        }

        // Actualización del monto total acumulado
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
        $compra = Compra::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($request->has('fecha_compra')) {
                $compra->update(['fecha_compra' => $request->fecha_compra]);
            }

            if ($request->has('detalles')) {
                foreach ($request->detalles as $det) {
                    if (isset($det['detalle_id'])) {
                        $lote = Lote::where('detalle_compra_id', $det['detalle_id'])->first();
                        if ($lote) {
                            $lote->update([
                                'codigo_lote' => $det['codigo_lote'] ?? $lote->codigo_lote,
                                'fecha_expiracion' => $det['fecha_expiracion'] ?? $lote->fecha_expiracion
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Compra actualizada correctamente',
                'compra' => $compra->fresh('detalles.producto', 'detalles.lotes')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
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