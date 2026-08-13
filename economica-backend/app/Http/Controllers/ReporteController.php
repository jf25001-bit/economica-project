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
                //aqui se quiero saber cuántos artículos se vendieron en cada venta."
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_ventas WHERE venta_id = v.id) as articulos')//cuantos prodcutos se vendieron en cada venta, sumando la cantidad de cada producto en el detalle de ventas, usando COALESCE para evitar nulls
            )
            ->orderBy('v.fecha_venta', 'desc');
//filtra de esa fecha hasta esa fecha, usando whereDate para comparar solo la parte de fecha sin importar la hora
        if ($request->fecha_inicio) {
            $ventasQuery->whereDate('v.fecha_venta', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $ventasQuery->whereDate('v.fecha_venta', '<=', $request->fecha_fin);
        }
    //Esto sirve para limpiar los datos. El map toma cada venta y la transforma en un array con los campos que queremos mostrar, formateando la fecha y asegurando que el total sea un número decimal. Si el cliente es null, se muestra "Consumidor final".
        $ventas = $ventasQuery->get()->map(fn($v) => [
            'id'           => $v->id,
            'cliente'      => $v->cliente ?? 'Consumidor final',
            //carbon es una librería de manejo de fechas que facilita el formateo y manipulación de fechas en PHP. Aquí se formatea la fecha de venta al formato día/mes/año para una presentación más amigable.
            //y carbon lo convierte a 15/09/25 mas bonito para mostar
            'fecha'        => Carbon::parse($v->fecha)->format('d/m/Y'),
            'articulos'    => $v->articulos,
            'total'        => (float) $v->total,
        ]);
        //despues hace lo mismo pero para las compras, con la diferencia de que solo se toman en cuenta las compras que ya fueron completadas, asi no se toman en cuenta las compras que estan en proceso o canceladas
        $comprasQuery = DB::table('compras as c')
            ->select(
                'c.id',
                'c.fecha_compra as fecha',
                'c.estado',
                'c.total',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM detalle_compras WHERE compra_id = c.id) as productos')
            )
            ->where('c.estado', 'completada') //en reportes solo aparecen las compras completadas, asi no se muestran las compras que estan en proceso o canceladas
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
        //calcula totales sumando la columna total de cada venta y compra, usando sum para sumar los totales de las colecciones resultantes. Esto nos da el total de ingresos por ventas y el total de gastos por compras en el periodo seleccionado.
        $totalCaja     = $ventas->sum('total');
        $totalCompras  = $compras->sum('total');
//devuelve todos los datos empaquetados en un array asociativo para que puedan ser usados fácilmente en otras partes del código, como en la generación de reportes o respuestas JSON.
        return compact('ventas', 'compras', 'totalCaja', 'totalCompras');
    }

    public function datosTarjetas(Request $request)
    {
        try {
            $filtro = $request->query('periodo', 'mes');
            //se crean los motores de busqueda usando query builder de laravel para evitar problemas de null al usar sum en consultas con filtros
            //crea consultas pero aun no la ejecuta solo prepara
            $queryVentas = DB::table('ventas');
            $queryCompras = DB::table('compras')->where('estado', 'completada'); 
  
            //aqui se aplican los filtros de fecha dependiendo del periodo seleccionado, usando las funciones de Carbon para obtener las fechas correspondientes
            if ($filtro === 'dia') {
                $queryVentas->whereDate('fecha_venta', Carbon::today());//filtro diario solo del dia de hoy
                $queryCompras->whereDate('fecha_compra', Carbon::today());
            } elseif ($filtro === 'semana') {
                $queryVentas->whereBetween('fecha_venta', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);//filtro semanal
                $queryCompras->whereBetween('fecha_compra', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } else {
                $queryVentas->whereMonth('fecha_venta', Carbon::now()->month)->whereYear('fecha_venta', Carbon::now()->year);
                $queryCompras->whereMonth('fecha_compra', Carbon::now()->month)->whereYear('fecha_compra', Carbon::now()->year);
            }
            $ventasSum = $queryVentas->sum('total') ?? 0;  //se suman las ventas dando el total$
            $comprasSum = $queryCompras->sum('total') ?? 0; //suma laas compras dando el  total $$
            
            //cuantos productos hay en total
            $productosTotales = DB::table('productos')->count() ?? 0; 
            //busca en la tabla productos aquellos que tengan un stock menor
            $stockBajo = DB::table('productos')->where('stock', '<=', 5)->count() ?? 0;
           //devuelve los datos en formato JSON con un status 200 si todo sale bien, incluyendo las ventas, compras, total de productos y productos con stock bajo. Si ocurre un error, devuelve un mensaje de error con status 500.
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
//funcion para devolver un resumen completo
    public function resumenJson(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $datos = $this->obtenerDatos($request);//trae todo lo que calculamos

        return response()->json([
            'resumen' => [
                'ingresos_caja' => $datos['totalCaja'],
                'fiado_total'   => 0,
                'total_compras' => $datos['totalCompras'],
                'balance_neto'  => $datos['totalCaja'] - $datos['totalCompras'], //esto es ventas y compras
            ],
            'ventas'  => $datos['ventas']->values(),
            'compras' => $datos['compras']->values(),
        ]);
    }
//funcion que genera los pdfs
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
        //reporte compras o ventas dependiendo del tipo seleccionado, con los mismos filtros de fecha, y calcula el gran total sumando la columna total de cada venta o compra para mostrarlo en el reporte.
            if ($tipo === 'compras') {
                $datos = DB::table('compras')
                    ->where('estado', 'completada') 
                    ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                    ->orderBy('fecha_compra', 'desc')
                    ->get();
                    
                $granTotal = $datos->sum('total');
                return view('reportes.pdf_general', compact('datos', 'periodo', 'granTotal', 'tipo'));
            }
            //reporte ventas
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
