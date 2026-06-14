<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    private function obtenerDatos(Request $request): array
    {
        $ventasQuery = DB::table('ventas as v')
            ->select(
                'v.id',
                'v.fecha_venta as fecha',
                'v.cliente',
                'v.total',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_ventas WHERE venta_id = v.id) as articulos')
            )
            ->orderBy('v.fecha_venta', 'desc');

        if ($request->fecha_inicio) {
            $ventasQuery->whereDate('v.fecha_venta', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $ventasQuery->whereDate('v.fecha_venta', '<=', $request->fecha_fin);
        }

        $ventas = $ventasQuery->get()->map(fn($v) => [
            'id'           => $v->id,
            'cliente'      => $v->cliente ?? 'Consumidor final',
            'fecha'        => Carbon::parse($v->fecha)->format('d/m/Y'),
            'articulos'    => $v->articulos,
            'total'        => (float) $v->total,
        ]);

        $comprasQuery = DB::table('compras as c')
            ->select(
                'c.id',
                'c.fecha_compra as fecha',
                'c.estado',
                'c.total',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_compras WHERE compra_id = c.id) as productos')
            )
            ->where('c.estado', 'completada') 
            ->orderBy('c.fecha_compra', 'desc');

        if ($request->fecha_inicio) {
            $comprasQuery->whereDate('c.fecha_compra', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $comprasQuery->whereDate('c.fecha_compra', '<=', $request->fecha_fin);
        }

        $compras = $comprasQuery->get()->map(fn($c) => [
            'id'        => $c->id,
            'fecha'     => Carbon::parse($c->fecha)->format('d/m/Y'),
            'productos' => $c->productos,
            'total'     => (float) $c->total,
        ]);

        $totalCaja     = $ventas->sum('total');
        $totalCompras  = $compras->sum('total');

        return compact('ventas', 'compras', 'totalCaja', 'totalCompras');
    }

    public function datosTarjetas(Request $request)
    {
        try {
            $filtro = $request->query('periodo', 'mes');
            
            $queryVentas = DB::table('ventas');
            $queryCompras = DB::table('compras')->where('estado', 'completada'); 

            if ($filtro === 'dia') {
                $queryVentas->whereDate('fecha_venta', Carbon::today());
                $queryCompras->whereDate('fecha_compra', Carbon::today());
            } elseif ($filtro === 'semana') {
                $queryVentas->whereBetween('fecha_venta', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $queryCompras->whereBetween('fecha_compra', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } else {
                $queryVentas->whereMonth('fecha_venta', Carbon::now()->month)->whereYear('fecha_venta', Carbon::now()->year);
                $queryCompras->whereMonth('fecha_compra', Carbon::now()->month)->whereYear('fecha_compra', Carbon::now()->year);
            }

            $ventasSum = $queryVentas->sum('total') ?? 0;
            $comprasSum = $queryCompras->sum('total') ?? 0;
            
            $productosTotales = DB::table('productos')->count() ?? 0;
            $stockBajo = DB::table('productos')->where('stock', '<=', 5)->count() ?? 0;

            return response()->json([
                'ventas_mes' => (float) $ventasSum,
                'compras_mes' => (float) $comprasSum,
                'productos_totales' => (int) $productosTotales,
                'stock_bajo' => (int) $stockBajo
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno en el servidor',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function resumenJson(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $datos = $this->obtenerDatos($request);

        return response()->json([
            'resumen' => [
                'ingresos_caja' => $datos['totalCaja'],
                'fiado_total'   => 0,
                'total_compras' => $datos['totalCompras'],
                'balance_neto'  => $datos['totalCaja'] - $datos['totalCompras'],
            ],
            'ventas'  => $datos['ventas']->values(),
            'compras' => $datos['compras']->values(),
        ]);
    }

    public function reporteGeneral(Request $request)
    {
        try {
            $tipo = $request->query('tipo', 'general');
            $periodo = $request->query('periodo', 'mes');
            
            $fechaInicio = Carbon::now()->startOfMonth();
            $fechaFin = Carbon::now()->endOfMonth();

            if ($periodo === 'dia') {
                $fechaInicio = Carbon::today()->startOfDay();
                $fechaFin = Carbon::today()->endOfDay();
            } elseif ($periodo === 'semana') {
                $fechaInicio = Carbon::now()->startOfWeek();
                $fechaFin = Carbon::now()->endOfWeek();
            }

            if ($tipo === 'general') {
                $ventas = DB::table('ventas')
                    ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                    ->get();
                
                $compras = DB::table('compras')
                    ->where('estado', 'completada') 
                    ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                    ->get();

                $totalVentas = $ventas->sum('total');
                $totalCompras = $compras->sum('total');
                $balanceNeto = $totalVentas - $totalCompras;

                return view('reportes.pdf_general', [
                    'tipo' => 'general',
                    'periodo' => $periodo,
                    'ventas' => $ventas,
                    'compras' => $compras,
                    'totalVentas' => $totalVentas,
                    'totalCompras' => $totalCompras,
                    'balanceNeto' => $balanceNeto
                ]);
            }

            if ($tipo === 'compras') {
                $datos = DB::table('compras')
                    ->where('estado', 'completada') 
                    ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                    ->orderBy('fecha_compra', 'desc')
                    ->get();
                    
                $granTotal = $datos->sum('total');
                return view('reportes.pdf_general', compact('datos', 'periodo', 'granTotal', 'tipo'));
            }

            $datos = DB::table('ventas')
                ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_venta', 'desc')
                ->get();
                
            $granTotal = $datos->sum('total');
            return view('reportes.pdf_general', compact('datos', 'periodo', 'granTotal', 'tipo'));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar reporte', 'detalle' => $e->getMessage()], 500);
        }
    }
}
