<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // 1. MÉTODO PRIVADO PARA COMPARTIR CONSULTAS ENTRE LA API Y EL PDF
    private function obtenerDatos(Request $request): array
    {
        $ventasQuery = DB::table('ventas as v')
            ->join('metodos_pagos as mp', 'v.metodo_pago_id', '=', 'mp.id')
            ->leftJoin('creditos as c', 'v.id', '=', 'c.venta_id')
            ->leftJoin('clientes_creditos as cc', 'c.cliente_credito_id', '=', 'cc.id')
            ->select(
                'v.correlativo',
                'v.fecha',
                'v.tipo_cliente',
                'v.estado',
                'v.total',
                'mp.nombre as metodo_pago',
                'cc.nombre as cliente_credito_nombre',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_ventas WHERE venta_id = v.id) as articulos')
            )
            ->whereIn('v.estado', ['PAGADA', 'CREDITO'])
            ->orderBy('v.fecha', 'desc');

        if ($request->fecha_inicio) {
            $ventasQuery->whereDate('v.fecha', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $ventasQuery->whereDate('v.fecha', '<=', $request->fecha_fin);
        }

        $ventas = $ventasQuery->get()->map(fn($v) => [
            'correlativo'  => $v->correlativo,
            'cliente'      => $v->estado === 'CREDITO'
                                ? ($v->cliente_credito_nombre ?? 'Sin nombre')
                                : 'Consumidor final',
            'fecha'        => Carbon::parse($v->fecha)->format('d/m/Y'),
            'tipo_cliente' => $v->tipo_cliente,
            'metodo_pago'  => $v->metodo_pago,
            'articulos'    => $v->articulos,
            'total'        => (float) $v->total,
            'estado'       => $v->estado,
        ]);

        $comprasQuery = DB::table('compras as c')
            ->join('proveedores as p', 'c.proveedor_id', '=', 'p.id')
            ->select(
                'p.nombre as proveedor',
                'p.telefono',
                'c.numero_factura',
                'c.fecha_registro',
                'c.total',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_compras WHERE compra_id = c.id) as productos')
            )
            ->where('c.estado', 'REGISTRADA')
            ->orderBy('c.fecha_registro', 'desc');

        if ($request->fecha_inicio) {
            $comprasQuery->whereDate('c.fecha_registro', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $comprasQuery->whereDate('c.fecha_registro', '<=', $request->fecha_fin);
        }

        $compras = $comprasQuery->get()->map(fn($c) => [
            'proveedor'      => $c->proveedor,
            'telefono'       => $c->telefono,
            'numero_factura' => $c->numero_factura,
            'fecha'          => Carbon::parse($c->fecha_registro)->format('d/m/Y'),
            'productos'      => $c->productos,
            'total'          => (float) $c->total,
        ]);

        $ventasPagadas = $ventas->where('estado', 'PAGADA');
        $ventasCredito = $ventas->where('estado', 'CREDITO');
        $totalCaja     = $ventasPagadas->sum('total');
        $totalDeudas   = $ventasCredito->sum('total');
        $totalCompras  = $compras->sum('total');

        return compact(
            'ventas', 'compras',
            'totalCaja', 'totalDeudas', 'totalCompras',
            'ventasPagadas', 'ventasCredito'
        );
    }

    // 2. ENDPOINT PARA LAS TARJETAS SUPERIORES (ESTADÍSTICAS MENSURABLES)
    public function datosTarjetas(Request $request)
{
    try {
        $filtro = $request->query('periodo', 'mes');
        
        $queryVentas = DB::table('ventas');
        $queryCompras = DB::table('compras');

        if ($filtro === 'dia') {
            $queryVentas->whereDate('created_at', Carbon::today()); //
            $queryCompras->whereDate('created_at', Carbon::today());
        } elseif ($filtro === 'semana') {
            $queryVentas->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $queryCompras->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } else {
            $queryVentas->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
            $queryCompras->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        $ventasSum = $queryVentas->sum('total') ?? 0;
        $comprasSum = $queryCompras->sum('total') ?? 0;
        
        // Contar productos (tablas que siempre existen)
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
            'mensaje' => $e->getMessage(),
            'linea' => $e->getLine()
        ], 500);
    }
}

    // 3. ENDPOINT PARA EL RESUMEN DE LAS TABLAS EN VUE
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
                'fiado_total'   => $datos['totalDeudas'],
                'total_compras' => $datos['totalCompras'],
                'balance_neto'  => $datos['totalCaja'] - $datos['totalCompras'],
            ],
            'ventas'  => $datos['ventas']->values(),
            'compras' => $datos['compras']->values(),
        ]);
    }

    // 4. ENDPOINT QUE RENDERIZA EL PDF
    public function reporteGeneral(Request $request)
    {
    try {
        $periodo = $request->query('periodo', 'mes');
        $query = DB::table('ventas');

        if ($periodo === 'dia') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($periodo === 'semana') {
            $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
        } else {
            $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                  ->whereYear('created_at', \Carbon\Carbon::now()->year);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        // 1. Calculamos el total acumulado de las ventas obtenidas
        $granTotal = $ventas->sum('total');

        // 2. Retornamos una vista HTML hermosa que se auto-imprime como PDF
        return view('reportes.pdf_general', compact('ventas', 'periodo', 'granTotal'));

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error en el reporte', 'detalle' => $e->getMessage()], 500);
    }
}
}
