<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
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
        $hoy = now('America/El_Salvador')->toDateString();

        $request->validate([
            'fecha_compra' => 'nullable|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.proveedor_id' => 'required|exists:proveedores,id',
            'detalles.*.cantidad' => 'required|integer|min:1|max:100000',
            'detalles.*.unidades_por_paquete' => 'nullable|integer|min:1|max:100000',
            'detalles.*.precio_compra' => 'required|numeric|gt:0',
            'detalles.*.codigo_lote' => 'nullable|string|max:100',
            'detalles.*.fecha_expiracion' => 'nullable|date|after_or_equal:' . $hoy
        ], $this->mensajesValidacion());

        DB::beginTransaction();
        try {
            $compra = Compra::create([
                'fecha_compra' => $request->fecha_compra ? \Carbon\Carbon::parse($request->fecha_compra)->setTimeFrom(now()) : now(),
                'total' => 0
            ]);

            $totalGeneral = 0;

            foreach ($request->detalles as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                if ($producto->anulado) {
                    DB::rollBack();

                    return response()->json([
                        'message' => "El producto {$producto->nombre} está anulado y no se puede comprar."
                    ], 422);
                }

                $paquetesComprados = (int) $item['cantidad'];
                $unidadesPorPaquete = isset($item['unidades_por_paquete']) && $item['unidades_por_paquete'] > 0
                    ? (int) $item['unidades_por_paquete']
                    : 1;

                $unidadesTotales = $paquetesComprados * $unidadesPorPaquete;
                $precioPaquete = (float) $item['precio_compra'];
                $subtotal = $paquetesComprados * $precioPaquete;
                $totalGeneral += $subtotal;

                $detalle = DetalleCompra::create([
                    'compra_id'              => $compra->id,
                    'producto_id'            => $producto->id,
                    'producto_nombre'        => $producto->nombre,
                    'producto_codigo_barras' => $producto->codigo_barras,
                    'proveedor_id'           => $item['proveedor_id'],
                    'proveedor_nombre'       => $this->nombreProveedor($item['proveedor_id']),
                    'cantidad'               => $paquetesComprados,
                    'unidades_por_paquete'   => $unidadesPorPaquete,
                    'precio_compra'          => $precioPaquete,
                    'subtotal'               => $subtotal
                ]);

                $codigoLote = !empty($item['codigo_lote'])
                    ? $item['codigo_lote']
                    : 'LOTE-C' . $compra->id . '-P' . $producto->id;

                Lote::create([
                    'detalle_compra_id'      => $detalle->id,
                    'producto_id'            => $producto->id,
                    'producto_nombre'        => $producto->nombre,
                    'producto_codigo_barras' => $producto->codigo_barras,
                    'codigo_lote'            => $codigoLote,
                    'fecha_expiracion'       => $item['fecha_expiracion'] ?? null,
                    'cantidad_inicial'       => $unidadesTotales,
                    'cantidad_actual'        => $unidadesTotales
                ]);

                $producto->increment('stock', $unidadesTotales);

                $producto->recalcularPrecios();
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

    public function anular($id)
    {
        return DB::transaction(function () use ($id) {
            $compra = Compra::with('detalles')->lockForUpdate()->findOrFail($id);

            if ($compra->anulado) {
                throw ValidationException::withMessages([
                    'compra' => 'Esta compra ya está anulada.',
                ]);
            }

            $lotes = Lote::whereIn('detalle_compra_id', $compra->detalles->pluck('id'))
                ->lockForUpdate()
                ->get();

            foreach ($lotes as $lote) {
                $diferencia = abs((float) $lote->cantidad_inicial - (float) $lote->cantidad_actual);

                if ($diferencia > 0.00001) {
                    throw ValidationException::withMessages([
                        'compra' => "No se puede anular: del lote {$lote->codigo_lote} ({$lote->producto_nombre}) ya se vendieron o descontaron unidades.",
                    ]);
                }
            }

            $aDescontar = [];

            foreach ($lotes as $lote) {
                if ($lote->vencido_descontado_at === null) {
                    $aDescontar[$lote->producto_id] = ($aDescontar[$lote->producto_id] ?? 0) + (float) $lote->cantidad_actual;
                }

                $lote->delete();
            }

            foreach ($compra->detalles->pluck('producto_id')->unique() as $productoId) {
                $producto = Producto::lockForUpdate()->findOrFail($productoId);
                $unidades = $aDescontar[$productoId] ?? 0;

                if ($unidades > 0) {
                    if ((float) $producto->stock + 0.00001 < $unidades) {
                        throw ValidationException::withMessages([
                            'compra' => "No se puede anular: el stock actual de {$producto->nombre} ({$producto->stock}) es menor a las {$unidades} unidades que ingresó esta compra.",
                        ]);
                    }

                    $producto->decrement('stock', $unidades);
                }

                $producto->recalcularPrecios();
            }

            $compra->update([
                'anulado'    => true,
                'anulado_at' => now(),
            ]);

            return response()->json([
                'message' => 'Compra anulada e inventario ajustado correctamente',
                'compra'  => $compra->fresh('detalles.producto', 'detalles.proveedor', 'detalles.lotes'),
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

    private function nombreProveedor($id): ?string
    {
        $proveedor = Proveedor::find($id);

        return $proveedor?->nombre_proveedor ?? $proveedor?->nombre;
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
            'detalles.*.fecha_expiracion.after_or_equal' => 'La fecha de expiración no puede ser anterior a hoy. No se pueden comprar productos caducados.',
        ];
    }
}