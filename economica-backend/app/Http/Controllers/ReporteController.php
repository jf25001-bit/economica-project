<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    private const PERIODOS = [
        'semana' => 'Semanal',
        'mes' => 'Mensual',
        'anio' => 'Anual',
        'rango' => 'Personalizado',
    ];

    public function reporteGeneral(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ventas,compras,empleado',
            'periodo' => 'required|in:semana,mes,anio,rango',
            'empleado_id' => 'required_if:tipo,empleado|nullable|exists:users,id',
            'fecha_inicio' => 'required_if:periodo,rango|nullable|date',
            'fecha_fin' => 'required_if:periodo,rango|nullable|date|after_or_equal:fecha_inicio',
        ]);

        [$inicio, $fin] = $this->rangoFechas($request);

        $base = [
            'periodo' => self::PERIODOS[$request->periodo],
            'inicio' => $inicio,
            'fin' => $fin,
        ];

        try {
            return match ($request->tipo) {
                'ventas' => $this->pdfVentas($base),
                'compras' => $this->pdfCompras($base),
                'empleado' => $this->pdfEmpleado($base, (int) $request->empleado_id),
            };
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el PDF',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }

    private function rangoFechas(Request $request): array
    {
        $ahora = Carbon::now();

        return match ($request->periodo) {
            'semana' => [$ahora->copy()->startOfWeek(), $ahora->copy()->endOfWeek()],
            'anio' => [$ahora->copy()->startOfYear(), $ahora->copy()->endOfYear()],
            'rango' => [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay(),
            ],
            default => [$ahora->copy()->startOfMonth(), $ahora->copy()->endOfMonth()],
        };
    }

    private function pdfVentas(array $base)
    {
        $ventas = Venta::with(['detalles.producto', 'usuario'])
            ->whereBetween('fecha_venta', [$base['inicio'], $base['fin']])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $granTotal = $ventas->sum('total');
        $promedio = $ventas->count() ? $granTotal / $ventas->count() : 0;

        return Pdf::loadView('reportes.pdf_ventas', $base + compact('ventas', 'granTotal', 'promedio'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_ventas.pdf');
    }

    private function pdfCompras(array $base)
    {
        $compras = Compra::with(['detalles.producto', 'detalles.proveedor'])
            ->whereBetween('fecha_compra', [$base['inicio'], $base['fin']])
            ->orderBy('fecha_compra', 'desc')
            ->get();

        $granTotal = $compras->sum('total');
        $promedio = $compras->count() ? $granTotal / $compras->count() : 0;

        return Pdf::loadView('reportes.pdf_compras', $base + compact('compras', 'granTotal', 'promedio'))
            ->setPaper('a4', 'landscape')
            ->stream('reporte_compras.pdf');
    }

    private function pdfEmpleado(array $base, int $empleadoId)
    {
        $empleado = User::findOrFail($empleadoId);

        $ventas = Venta::with('detalles.producto')
            ->where('user_id', $empleadoId)
            ->whereBetween('fecha_venta', [$base['inicio'], $base['fin']])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $granTotal = $ventas->sum('total');
        $promedio = $ventas->count() ? $granTotal / $ventas->count() : 0;

        return Pdf::loadView('reportes.pdf_ventas_empleado', $base + compact('ventas', 'empleado', 'granTotal', 'promedio'))
            ->stream('reporte_empleado.pdf');
    }
}