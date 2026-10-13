<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    public function index()
    {
        try {

            try {
                $this->descontarVencidos();
            } catch (\Throwable $e) {
                report($e);
            }

            $lotes = Lote::with([
                'producto'
            ])->get();

            return response()->json(
                $lotes
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al obtener lotes',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $lote = Lote::with([
                'producto'
            ])->findOrFail($id);

            return response()->json(
                $lote
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Lote no encontrado'
            ], 404);
        }
    }

    public function update(
        Request $request,
        $id
    ) {
        try {

            $lote =
                Lote::findOrFail($id);

            $request->validate([
                'numero_lote' =>
                    'nullable|string|max:100',

                'fecha_expiracion' =>
                    'nullable|date',

                'cantidad_actual' =>
                    'nullable|integer|min:0'
            ]);

            $estabaDescontado = $lote->vencido_descontado_at !== null;

            $lote->update(
                $request->all()
            );

            if ($estabaDescontado && !$this->estaVencido($lote->fecha_expiracion)) {
                Producto::where('id', $lote->producto_id)
                    ->increment('stock', $lote->cantidad_actual);

                $lote->vencido_descontado_at = null;
                $lote->save();
            }

            return response()->json([
                'message' =>
                    'Lote actualizado',

                'data' =>
                    $lote
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al actualizar lote',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $lote =
                Lote::findOrFail($id);

            $lote->delete();

            return response()->json([
                'message' =>
                    'Lote eliminado'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al eliminar lote'
            ], 500);
        }
    }

    private function hoy(): string
    {
        return now('America/El_Salvador')->toDateString();
    }

    private function estaVencido($fecha): bool
    {
        $fecha = substr((string) $fecha, 0, 10);

        return $fecha !== '' && $fecha < $this->hoy();
    }

    private function descontarVencidos(): void
    {
        $ids = Lote::query()
            ->whereDate('fecha_expiracion', '<', $this->hoy())
            ->where('cantidad_actual', '>', 0)
            ->whereNull('vencido_descontado_at')
            ->pluck('id');

        foreach ($ids as $id) {
            DB::transaction(function () use ($id) {
                $lote = Lote::query()->lockForUpdate()->find($id);

                if (!$lote || $lote->vencido_descontado_at || $lote->cantidad_actual <= 0) {
                    return;
                }

                $cantidad = number_format((float) $lote->cantidad_actual, 2, '.', '');

                Producto::where('id', $lote->producto_id)
                    ->update(['stock' => DB::raw("GREATEST(stock - $cantidad, 0)")]);

                $lote->vencido_descontado_at = now();
                $lote->save();
            });
        }
    }
}