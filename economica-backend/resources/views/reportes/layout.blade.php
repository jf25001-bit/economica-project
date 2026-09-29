<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('titulo')</title>
    <style>
        @page { margin: 32px 30px 50px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #2d3748; margin: 0; }
        .encabezado { width: 100%; border-collapse: collapse; border-bottom: 3px solid #2b3a4a; margin-bottom: 14px; }
        .encabezado td { padding: 0 0 8px 0; vertical-align: bottom; }
        .marca { font-size: 19px; font-weight: bold; color: #2b3a4a; }
        .sub { font-size: 8px; color: #718096; text-transform: uppercase; letter-spacing: 1px; }
        .titulo { font-size: 13px; font-weight: bold; color: #5b80b0; text-transform: uppercase; }
        .meta { font-size: 8px; color: #718096; margin-top: 2px; }
        .derecha { text-align: right; }
        .centro { text-align: center; }
        .resumen { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 14px -8px; }
        .resumen td { background: #f1f5f9; border-left: 3px solid #5b80b0; padding: 8px 10px; }
        .etq { font-size: 7px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; }
        .val { font-size: 14px; font-weight: bold; color: #2b3a4a; margin-top: 2px; }
        .val-sm { font-size: 10px; font-weight: bold; color: #2b3a4a; margin-top: 3px; }
        .bloque { margin-bottom: 16px; page-break-inside: avoid; }
        table.datos { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.datos th { background: #2b3a4a; color: #ffffff; font-size: 8px; text-transform: uppercase; padding: 7px; text-align: left; }
        table.datos th.derecha { text-align: right; }
        table.datos th.centro { text-align: center; }
        table.datos td { border-bottom: 1px solid #e2e8f0; padding: 5px 7px; vertical-align: middle; }
        table.datos td.cab { background: #f1f5f9; font-weight: bold; font-size: 10px; color: #2b3a4a; padding: 8px 7px; border-bottom: 2px solid #5b80b0; }
        table.datos.detalle th { background: #5b80b0; padding: 5px 7px; }
        table.datos.detalle tr.par td { background: #f8fafc; }
        table.total-barra { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.total-barra td { background: #2b3a4a; color: #ffffff; font-weight: bold; font-size: 11px; padding: 9px 10px; }
        .vacio { text-align: center; padding: 24px 0; color: #a0aec0; font-style: italic; }
        .pie { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8px; color: #a0aec0; }
        .pagina:before { content: counter(page); }
    </style>
</head>
<body>
    <div class="pie">La Económica &nbsp;|&nbsp; Página <span class="pagina"></span></div>

    <table class="encabezado">
        <tr>
            <td>
                <div class="marca">LA ECONÓMICA</div>
                <div class="sub">Reporte de operaciones</div>
            </td>
            <td class="derecha">
                <div class="titulo">@yield('titulo')</div>
                <div class="meta">Periodo {{ strtolower($periodo) }}: {{ $inicio->format('d/m/Y') }} al {{ $fin->format('d/m/Y') }}</div>
                <div class="meta">Generado el {{ now()->format('d/m/Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    @yield('contenido')
</body>
</html>