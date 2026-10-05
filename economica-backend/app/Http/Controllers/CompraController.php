<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function index()
    {
        try {
            $compras = Compra::with([
                'detalles.producto',
                'detalles.proveedor',
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
            'detalles.*.proveedor_id' => 'required|exists:proveedores,id',
            'detalles.*.cantidad' => 'required|integer|min:1|max:100000',
            'detalles.*.unidades_por_paquete' => 'nullable|integer|min:1|max:100000',
            'detalles.*.precio_compra' => 'required|numeric|gt:0',
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date'
        ], $this->mensajesValidacion());

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

                $detalle = DetalleCompra::create([
                    'compra_id'            => $compra->id,
                    'producto_id'          => $producto->id,
                    'proveedor_id'         => $item['proveedor_id'],
                    'cantidad'             => $paquetesComprados,
                    'unidades_por_paquete' => $unidadesPorPaquete,
                    'precio_compra'        => $precioPaquete,
                    'subtotal'             => $subtotal
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
                'compra' => $compra->fresh('detalles.producto', 'detalles.proveedor', 'detalles.lotes')
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
            'detalles' => 'required|array|min:1',
            'detalles.*.detalle_id' => 'required|integer',
            'detalles.*.proveedor_id' => 'required|exists:proveedores,id',
            'detalles.*.cantidad' => 'required|integer|min:1|max:100000',
            'detalles.*.unidades_por_paquete' => 'required|integer|min:1|max:100000',
            'detalles.*.precio_compra' => 'required|numeric|gt:0',
            'detalles.*.fecha_expiracion' => 'nullable|date',
        ], $this->mensajesValidacion());

        return DB::transaction(function () use ($request, $id) {
            $compra = Compra::with('detalles.lotes')->findOrFail($id);

            foreach ($request->detalles as $i => $det) {
                $detalle = $compra->detalles->firstWhere('id', (int) $det['detalle_id']);

                if (!$detalle) {
                    throw ValidationException::withMessages([
                        "detalles.$i.detalle_id" => 'Uno de los ítems no pertenece a esta compra.',
                    ]);
                }

                // El producto no se puede cambiar en una compra ya registrada
                if (isset($det['producto_id']) && (int) $det['producto_id'] !== (int) $detalle->producto_id) {
                    throw ValidationException::withMessages([
                        "detalles.$i.producto_id" => 'No se puede cambiar el producto de una compra ya registrada.',
                    ]);
                }

                $paquetes = (int) $det['cantidad'];
                $unidPorPaquete = (int) $det['unidades_por_paquete'];
                $precioPaquete = (float) $det['precio_compra'];

                $unidadesNuevas = $paquetes * $unidPorPaquete;
                $unidadesAnteriores = $detalle->cantidad * $detalle->unidades_por_paquete;
                $diferencia = $unidadesNuevas - $unidadesAnteriores;

                $lote = $detalle->lotes->first();

                if ($lote) {
                    $vendidas = $lote->cantidad_inicial - $lote->cantidad_actual;

                    if ($unidadesNuevas < $vendidas) {
                        throw ValidationException::withMessages([
                            "detalles.$i.cantidad" => "No puedes dejar este ítem en $unidadesNuevas unidades: del lote ya se han descontado $vendidas.",
                        ]);
                    }

                    // Código de lote intacto; la cantidad actual se ajusta por la diferencia
                    $lote->update([
                        'fecha_expiracion' => $det['fecha_expiracion'] ?? null,
                        'cantidad_inicial' => $unidadesNuevas,
                        'cantidad_actual'  => $lote->cantidad_actual + $diferencia,
                    ]);
                }

                if ($diferencia !== 0) {
                    Producto::where('id', $detalle->producto_id)->increment('stock', $diferencia);
                }

                $detalle->update([
                    'proveedor_id'         => $det['proveedor_id'],
                    'cantidad'             => $paquetes,
                    'unidades_por_paquete' => $unidPorPaquete,
                    'precio_compra'        => $precioPaquete,
                    'subtotal'             => $paquetes * $precioPaquete,
                ]);
            }

            // La fecha de la compra no se modifica; el total se recalcula desde los ítems
            $compra->update(['total' => $compra->detalles()->sum('subtotal')]);

            return response()->json([
                'message' => 'Orden de compra actualizada con éxito',
                'compra'  => $compra->fresh('detalles.producto', 'detalles.proveedor', 'detalles.lotes')
            ], 200);
        });
    }

    public function show($id)
    {
        try {
            $compra = Compra::with([
                'detalles.producto',
                'detalles.proveedor',
                'detalles.lotes'
            ])->findOrFail($id);

            return response()->json($compra);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Compra no encontrada'
            ], 404);
        }
    }

    private function mensajesValidacion(): array
    {
        return [
            'detalles.*.cantidad.integer' => 'La cantidad de paquetes debe ser un número entero (no se permiten fracciones como 1.5).',
            'detalles.*.cantidad.min' => 'La cantidad de paquetes debe ser al menos 1.',
            'detalles.*.cantidad.max' => 'La cantidad de paquetes es demasiado grande.',
            'detalles.*.unidades_por_paquete.integer' => 'Las unidades por paquete deben ser un número entero.',
            'detalles.*.unidades_por_paquete.min' => 'Las unidades por paquete deben ser al menos 1.',
            'detalles.*.unidades_por_paquete.max' => 'Las unidades por paquete son demasiado grandes.',
            'detalles.*.precio_compra.numeric' => 'El precio del paquete debe ser un número.',
            'detalles.*.precio_compra.gt' => 'El precio del paquete debe ser mayor a 0.',
        ];
    }
}