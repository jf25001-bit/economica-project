<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Ventas - La Económica</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #46674A; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; color: #46674A; text-transform: uppercase; }
        .subtitle { font-size: 12px; color: #666; letter-spacing: 2px; text-transform: uppercase; margin-top: 5px; }
        .info-reporte { margin-bottom: 20px; font-size: 14px; }
        table { w_width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { bg_background-color: #46674A; color: white; padding: 10px; text-align: left; font-size: 14px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; font-size: 13px; }
        tr:nth-child(even) { bg_background-color: #f9f9f9; }
        .total-row { font-weight: bold; bg_background-color: #eafee9 !important; }
        .text-right { text-align: right; }
        .no-datos { text-align: center; color: #999; padding: 30px; font-style: italic; }
        @media print {
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">La Económica</div>
        <div class="subtitle">Reporte General de Ventas</div>
    </div>

    <div class="info-reporte">
        <strong>Periodo Consultado:</strong> Reporte del {{ ucfirst($periodo) }} <br>
        <strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i A') }}
    </div>

    <table width="100%">
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Fecha de Registro</th>
                <th>Estado</th>
                <th class="text-right">Monto Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
                <tr>
                    <td>#{{ $venta->id }}</td>
                    <td>{{ date('d/m/Y H:i A', strtotime($venta->created_at ?? $venta->fecha ?? 'now')) }}</td>
                    <td><span style="color: green; font-weight: bold;">{{ $venta->estado ?? 'COMPLETADA' }}</span></td>
                    <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="no-datos">No se encontraron ventas registradas en este periodo de tiempo.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL ACUMULADO:</td>
                <td class="text-right">${{ number_format($granTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>