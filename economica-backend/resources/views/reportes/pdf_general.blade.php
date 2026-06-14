<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Gerencial - La Económica</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; margin: 0; }
        .header { margin-bottom: 25px; border-bottom: 3px solid #2e7d32; padding-bottom: 10px; }
        .logo { font-size: 26px; font-weight: bold; color: #2e7d32; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 13px; color: #666; margin-top: 5px; }
        .title-reporte { text-align: center; font-size: 20px; font-weight: bold; color: #1a237e; margin: 20px 0; text-transform: uppercase; }
        .meta-box { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .meta-box td { padding: 6px; font-size: 13px; }
        .kpi-container { width: 100%; margin-bottom: 30px; }
        .kpi-card { background: #f1f8e9; border: 1px solid #c5e1a5; border-radius: 4px; padding: 15px; text-align: center; }
        .kpi-val { font-size: 22px; font-weight: bold; color: #2e7d32; margin-top: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.data-table th { background-color: #2e7d32; color: white; font-size: 12px; font-weight: bold; text-transform: uppercase; padding: 10px; text-align: left; }
        table.data-table td { padding: 9px 10px; font-size: 12px; border-bottom: 1px solid #e0e0e0; }
        table.data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 11px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td class="logo">La Económica</td>
                <td class="text-right" style="font-size: 12px; color: #666;">
                    <strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
        <div class="subtitle">Sistema de Control de Inventario y Facturación Automatizada</div>
    </div>

    <table class="meta-box">
        <tr>
            <td style="width: 50%;"><strong>Filtro de Periodo:</strong> <span style="text-transform: uppercase;">{{ $periodo }}</span></td>
            <td class="text-right"><strong>Estado del Sistema:</strong> <span class="badge">Producción Activa</span></td>
        </tr>
    </table>

    @if($tipo === 'ventas_producto')
        <div class="title-reporte">Reporte de Ventas por Producto (Ranking de Demanda)</div>
        <p style="font-size: 13px; color: #555; text-align: center;">Muestra el desglose de los productos más vendidos y el volumen financiero recaudado en el periodo seleccionado.</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Top</th>
                    <th style="width: 50%;">Nombre del Producto</th>
                    <th style="width: 20%; text-align: center;">Cantidad Vendida</th>
                    <th style="width: 20%; text-align: right;">Total Recaudado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $index => $item)
                    <tr>
                        <td class="text-center"><strong>#{{ $index + 1 }}</strong></td>
                        <td>{{ $item->producto }}</td>
                        <td class="text-center"><span style="background: #e3f2fd; color: #0d47a1; padding: 2px 6px; border-radius: 4px;">{{ $item->total_amount ?? $item->total_cantidad }} uds</span></td>
                        <td class="text-right"><strong>${{ number_format($item->total_recaudado, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color: #999; padding: 20px;">No se registraron transacciones de productos en este periodo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($tipo === 'general')
        <div class="title-reporte">Reporte de Balance General Financiero</div>
        
        <table class="kpi-container">
            <tr>
                <td style="width: 33%; padding-right: 10px;">
                    <div class="kpi-card">
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Total Ingresos (Ventas)</div>
                        <div class="kpi-val">${{ number_format($totalVentas, 2) }}</div>
                    </div>
                </td>
                <td style="width: 33%; padding: 0 5px;">
                    <div class="kpi-card" style="background: #fff3e0; border-color: #ffe0b2;">
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Total Egresos (Compras)</div>
                        <div class="kpi-val" style="color: #e65100;">${{ number_format($totalCompras, 2) }}</div>
                    </div>
                </td>
                <td style="width: 33%; padding-left: 10px;">
                    <div class="kpi-card" style="background: #e3f2fd; border-color: #bbdefb;">
                        <div style="font-size: 12px; color: #666; text-transform: uppercase;">Balance Neto Flujo</div>
                        <div class="kpi-val" style="color: #0d47a1;">${{ number_format($balanceNeto, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <h3 style="font-size: 14px; color: #2e7d32; text-transform: uppercase;">Resumen de Operaciones Recientes</h3>
        <p style="font-size: 12px; color: #666;">El documento consolida el flujo de caja total del periodo de forma global.</p>

    @else
        <div class="title-reporte">Reporte Analítico de {{ ucfirst($tipo) }}</div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Documento</th>
                    <th>Fecha de Registro</th>
                    <th class="text-right">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="color: #999; padding: 20px;">No se encontraron registros en el sistema para este rango.</td>
                    </tr>
                @endforelse
                @if(isset($granTotal) && $granTotal > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="2" class="text-right"><strong>TOTAL ACUMULADO:</strong></td>
                        <td class="text-right" style="color: #2e7d32; font-weight: bold; font-size: 14px;">${{ number_format($granTotal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <div class="footer">
        La Económica S.A de C.V. - Reporte Confidencial de Uso Interno - Página 1 de 1
    </div>

</body>
</html>