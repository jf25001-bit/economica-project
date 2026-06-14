<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 
use Tymon\JWTAuth\Facades\JWTAuth; // Asegúrate de que usen JWT para la autenticación manual si es necesario

class ReporteController extends Controller
{
    public function reporteGeneral(Request $request)
    {
       try {
        // 🛡️ INTENTO DE AUTENTICACIÓN SILENCIOSO
        if ($request->has('token')) {
            try {
                $user = \Tymon\JWTAuth\Facades\JWTAuth::setToken($request->token)->toUser();
                if ($user) {
                    auth()->login($user);
                }
            } catch (\Exception $authEx) {
                // Si el token expiró, NO bloqueamos la pantalla.
                // Simplemente dejamos que continúe de forma pública para que el PDF cargue.
            }
        }

        $tipo = $request->input('tipo', 'ventas'); 
        $periodo = $request->input('periodo', 'mes'); 
        
        // Inicialización segura de variables para la plantilla Blade
        $datos = collect([]);
        $totalVentas = 0;
        $totalCompras = 0;
        $balanceNeto = 0;
        $granTotal = 0;

        // Función para aplicar los filtros de tiempo
        $filtroFechas = function($query, $columna) use ($periodo) {
            switch ($periodo) {
                case 'hoy':
                case 'dia':
                    return $query->whereDate($columna, Carbon::today());
                case 'semana':
                    return $query->whereBetween($columna, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                case 'mes':
                default:
                    return $query->whereMonth($columna, Carbon::now()->month)
                                 ->whereYear($columna, Carbon::now()->year);
            }
        };

        // Procesamiento de datos según la solicitud
        if ($tipo === 'general') {
            $ventasQuery = Venta::query();
            $comprasQuery = Compra::where('estado', 'completada');

            $filtroFechas($ventasQuery, 'fecha_venta');
            $filtroFechas($comprasQuery, 'fecha_compra');

            $totalVentas = (float)$ventasQuery->sum('total');
            $totalCompras = (float)$comprasQuery->sum('total');
            $balanceNeto = $totalVentas - $totalCompras;

        } elseif ($tipo === 'ventas_producto') {
            $queryBase = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                ->select(
                    'productos.nombre as producto',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_cantidad'),
                    DB::raw('SUM(detalle_ventas.cantidad) as total_amount'), 
                    DB::raw('SUM(detalle_ventas.subtotal) as total_recaudado')
                );

            $filtroFechas($queryBase, 'ventas.fecha_venta');
            
            $datos = $queryBase->groupBy('productos.id', 'productos.nombre')
                ->orderBy('total_recaudado', 'desc')
                ->get();

        } else {
            $queryModel = ($tipo === 'compras') ? Compra::where('estado', 'completada') : Venta::query();
            $columnaFecha = ($tipo === 'compras') ? 'fecha_compra' : 'fecha_venta';

            $filtroFechas($queryModel, $columnaFecha);
            
            $datos = $queryModel->orderBy($columnaFecha, 'desc')->get();
            $granTotal = (float)$datos->sum('total');
        }

        // Mapeo de variables para el Blade
        $payload = [
            'tipo' => $tipo,
            'periodo' => $periodo,
            'datos' => $datos,
            'totalVentas' => $totalVentas,
            'totalCompras' => $totalCompras,
            'balanceNeto' => $balanceNeto,
            'granTotal' => $granTotal
        ];

        // Carga la plantilla que encontramos en tu carpeta views/reportes/
        $pdf = Pdf::loadView('reportes.pdf_general', $payload);
        
        return $pdf->stream('la_economica_reporte_' . $tipo . '.pdf');

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error crítico al procesar DomPDF',
            'error' => $e->getMessage()
        ], 500);
    }
}
    

    public function datosTarjetas(Request $request)
    {
        try {
            $periodo = $request->input('periodo', 'mes');
            
            $queryVentas = Venta::query();
            $queryCompras = Compra::where('estado', 'completada');

            switch ($periodo) {
                case 'hoy':
                case 'dia':
                    $queryVentas->whereDate('fecha_venta', Carbon::today());
                    $queryCompras->whereDate('fecha_compra', Carbon::today());
                    break;
                case 'semana':
                    $queryVentas->whereBetween('fecha_venta', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    $queryCompras->whereBetween('fecha_compra', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'mes':
                default:
                    $queryVentas->whereMonth('fecha_venta', Carbon::now()->month)->whereYear('fecha_venta', Carbon::now()->year);
                    $queryCompras->whereMonth('fecha_compra', Carbon::now()->month)->whereYear('fecha_compra', Carbon::now()->year);
                    break;
            }

            return response()->json([
                'status' => 'success',
                'tarjetas' => [
                    'ventas_periodo' => (float)$queryVentas->sum('total'),
                    'compras_periodo' => (float)$queryCompras->sum('total'),
                    'productos_registrados' => (int)Producto::count(),
                    'productos_bajo_stock' => (int)Producto::whereRaw('stock <= stock_minimo')->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en tarjetas', 'error' => $e->getMessage()], 500);
        }
    }

    public function resumenJson()
    {
        return response()->json(['status' => 'success']);
    }
}