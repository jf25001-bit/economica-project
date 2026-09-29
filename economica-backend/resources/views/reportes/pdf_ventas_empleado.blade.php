@extends('reportes.layout')

@section('titulo', 'Reporte de Ventas por Empleado')

@section('contenido')
    <table class="resumen">
        <tr>
            <td width="34%">
                <div class="etq">Empleado</div>
                <div class="val-sm">{{ trim($empleado->name . ' ' . $empleado->apellido) }}</div>
            </td>
            <td width="30%">
                <div class="etq">Correo</div>
                <div class="val-sm">{{ $empleado->email ?? 'N/A' }}</div>
            </td>
            <td width="18%">
                <div class="etq">Teléfono</div>
                <div class="val-sm">{{ $empleado->telefono ?: 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <table class="resumen">
        <tr>
            <td>
                <div class="etq">Ventas realizadas</div>
                <div class="val">{{ $ventas->count() }}</div>
            </td>
            <td>
                <div class="etq">Total vendido</div>
                <div class="val">${{ number_format($granTotal, 2) }}</div>
            </td>
            <td>
                <div class="etq">Venta promedio</div>
                <div class="val">${{ number_format($promedio, 2) }}</div>
            </td>
        </tr>
    </table>

    @forelse($ventas as $venta)
        @php $fecha = \Carbon\Carbon::parse($venta->fecha_venta); @endphp
        <div class="bloque">
            <table class="datos">
                <thead>
                    <tr>
                        <th width="10%">Venta</th>
                        <th width="15%">Fecha</th>
                        <th width="13%">Hora</th>
                        <th width="22%">Cliente</th>
                        <th width="14%" class="derecha">Recibido</th>
                        <th width="13%" class="derecha">Vuelto</th>
                        <th width="13%" class="derecha">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="cab">#{{ $venta->id }}</td>
                        <td class="cab">{{ $fecha->format('d/m/Y') }}</td>
                        <td class="cab">{{ $fecha->format('h:i A') }}</td>
                        <td class="cab">{{ $venta->cliente ?: 'Consumidor final' }}</td>
                        <td class="cab derecha">${{ number_format($venta->dinero_recibido ?? $venta->total, 2) }}</td>
                        <td class="cab derecha">${{ number_format($venta->vuelto ?? 0, 2) }}</td>
                        <td class="cab derecha">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="datos detalle">
                <thead>
                    <tr>
                        <th width="46%">Producto</th>
                        <th width="12%" class="centro">Cantidad</th>
                        <th width="20%" class="derecha">Precio unitario</th>
                        <th width="22%" class="derecha">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $detalle)
                        <tr class="{{ $loop->even ? 'par' : '' }}">
                            <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                            <td class="centro">{{ $detalle->cantidad }}</td>
                            <td class="derecha">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="derecha">${{ number_format($detalle->subtotal ?? $detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="vacio">Este empleado no registró ventas en el periodo seleccionado.</div>
    @endforelse

    @if($ventas->count())
        <table class="total-barra">
            <tr>
                <td class="derecha">TOTAL VENDIDO: ${{ number_format($granTotal, 2) }}</td>
            </tr>
        </table>
    @endif
@endsection