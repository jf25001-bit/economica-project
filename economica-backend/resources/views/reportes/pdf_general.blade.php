<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Ejecutivo - La Económica</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #2D3748; padding: 45px; background: #fff; line-height: 1.6; }
        
        /* Encabezado */
        .brand-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #E2E8F0; padding-bottom: 20px; margin-bottom: 25px; }
        .logo-section h1 { color: #46674A; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .logo-section p { color: #718096; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px; }
        .meta-section { text-align: right; font-size: 12px; color: #4A5568; }
        .meta-section span { color: #1A202C; font-weight: 600; }

        /* Barra del Título del Reporte */
        .report-title-bar { background-color: #F7FAFC; border-left: 4px solid #46674A; padding: 12px 18px; margin-bottom: 30px; border-radius: 0 4px 4px 0; }
        .report-title-bar h2 { font-size: 16px; text-transform: uppercase; color: #2D3748; letter-spacing: 0.5px; }
        
        
        .balance-container { display: flex; gap: 15px; margin-bottom: 30px; }
        .card { flex: 1; padding: 18px; border-radius: 8px; border: 1px solid #E2E8F0; }
        .card .title { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #718096; letter-spacing: 0.5px; }
        .card .value { font-size: 24px; font-weight: 800; margin-top: 6px; }
        
    
        .card.ventas { background-color: #EBF8FF; border-color: #BEE3F8; color: #2B6CB0; }
        .card.compras { background-color: #FFF5F5; border-color: #FED7D7; color: #C53030; }
        .card.balance { background-color: #F0FFF4; border-color: #C6F6D5; color: #22543D; }
        .card.negativo { background-color: #FFF5F5; border-color: #FED7D7; color: #9B1C1C; }

      
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 15px; border-radius: 6px; overflow: hidden; border: 1px solid #E2E8F0; }
        th { background-color: #46674A; color: white; font-size: 11px; font-weight: 600; text-transform: uppercase; padding: 12px 16px; text-align: left; letter-spacing: 0.5px; }
        td { padding: 12px 16px; border-bottom: 1px solid #E2E8F0; font-size: 13px; color: #4A5568; background: #fff; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background-color: #F8FAFC; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { background-color: #DEF7EC; color: #03543F; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .no-data { text-align: center; color: #A0AEC0; padding: 40px 0; font-style: italic; font-size: 13px; }
        .total-row td { background-color: #EDF2F7 !important; font-weight: 700; color: #1A202C; border-top: 2px solid #CBD5E0; font-size: 14px; }

        @media print {
            body { padding: 0; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

    <div class="brand-header">
        <div class="logo-section">
            <h1>LA ECONÓMICA</h1>
            <p>Panel de Control & Auditoría</p>
        </div>
        <div class="meta-section">
            <p>Fecha Emisión: <span>{{ date('d/m/Y') }}</span></p>
            <p>Hora Emisión: <span>{{ date('h:i A') }}</span></p>
            <p>Periodo: <span>{{ strtoupper($periodo) }}</span></p>
        </div>
    </div>

    <div class="report-title-bar">
        <h2>
            @if($tipo === 'general') Cierre de Caja & Balance General de Operaciones
            @elseif($tipo === 'ventas') Libro Auxiliar de Ventas Registradas
            @else Libro Auxiliar de Adquisiciones y Compras
            @endif
        </h2>
    </div>

    @if($tipo === 'general')
        <div class="balance-container">
            <div class="card ventas">
                <div class="title">(+) Total Ingresos (Ventas)</div>
                <div class="value">${{ number_format($totalVentas, 2) }}</div>
            </div>
            <div class="card compras">
                <div class="title">(-) Total Egresos (Compras)</div>
                <div class="value">${{ number_format($totalCompras, 2) }}</div>
            </div>
            <div class="card balance {{ $balanceNeto < 0 ? 'negativo' : '' }}">
                <div class="title">(=) Rendimiento Neto (Utilidad)</div>
                <div class="value">${{ number_format($balanceNeto, 2) }}</div>
            </div>
        </div>

        <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 8px; color: #46674A; text-transform: uppercase;">Resumen de Actividad Comercial</h3>
        <p style="font-size: 13px; color: #4A5568;">
            Durante el rango de tiempo evaluado, el establecimiento consolidó satisfactoriamente un flujo transaccional compuesto por <strong>{{ $ventas->count() }} ventas directas</strong> y un volumen de reabastecimiento logístico de <strong>{{ $compras->count() }} compras a proveedores</strong>, arrojando el balance financiero expresado en las tarjetas superiores.
        </p>

    @else
        <div class="balance-container" style="max-width: 320px;">
            <div class="card {{ $tipo === 'ventas' ? 'ventas' : 'compras' }}">
                <div class="title">Flujo Total Acumulado</div>
                <div class="value">${{ number_format($granTotal, 2) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="15%">Folio / ID</th>
                    <th width="45%">Fecha y Hora de Registro</th>
                    <th width="20%" class="text-center">Estado</th>
                    <th width="20%" class="text-right">Total Operado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $reg)
                    <tr>
                        <td style="font-weight: 600;">#{{ $reg->id }}</td>
                        <td>{{ date('d/m/Y h:i A', strtotime($reg->created_at)) }}</td>
                        <td class="text-center"><span class="badge">Procesado</span></td>
                        <td class="text-right" style="font-weight: 600; color: #1A202C;">${{ number_format($reg->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="no-data">No se registraron transacciones comerciales en el periodo seleccionado.</td></tr>
                @endforelse
                
                <tr class="total-row">
                    <td colspan="3" class="text-right">BALANCE TOTAL DE CAJA:</td>
                    <td class="text-right">${{ number_format($granTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>