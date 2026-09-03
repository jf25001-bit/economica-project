<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; border-bottom: 2px solid #0284c7; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 18px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0f172a; color: #ffffff; padding: 6px; text-align: left; font-size: 10px; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .total { margin-top: 15px; text-align: right; font-size: 13px; font-weight: bold; color: #059669; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS</h1>
        <p>Vendedor/Filtro: <strong>{{ $vendedor }}</strong> | Fecha: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th># Venta</th>
                <th>Vendedor / Usuario</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td>#{{ $venta->id }}</td>
                <td>{{ $venta->user->name ?? $venta->user->nombre ?? 'Usuario #'.$venta->user_id }}</td>
                <td>{{ $venta->created_at ? $venta->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>${{ number_format($venta->total ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 15px;">
                    Este usuario no posee registros de ventas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Monto Total: ${{ number_format($montoTotal, 2) }}
    </div>
</body>
</html>