@extends('reportes.layout')

@section('titulo', 'Reporte General de Ventas')

@section('contenido')
    <table class="resumen">
        <tr>
            <td>
                <div class="etq">Ventas registradas</div>
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
                        <th width="9%">Venta</th>
                        <th width="12%">Fecha</th>
                        <th width="10%">Hora</th>
                        <th width="22%">Vendedor</th>
                        <th width="17%">Cliente</th>
                        <th width="10%" class="derecha">Recibido</th>
                        <th width="10%" class="derecha">Vuelto</th>
                        <th width="10%" class="derecha">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="cab">#{{ $venta->id }}</td>
                        <td class="cab">{{ $fecha->format('d/m/Y') }}</td>
                        <td class="cab">{{ $fecha->format('h:i A') }}</td>
                        <td class="cab">{{ $venta->usuario ? trim($venta->usuario->name . ' ' . $venta->usuario->apellido) : 'N/A' }}</td>
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
        <div class="vacio">No se encontraron ventas en este periodo.</div>
    @endforelse

    @if($ventas->count())
        <table class="total-barra">
            <tr>
                <td class="derecha">TOTAL VENDIDO: ${{ number_format($granTotal, 2) }}</td>
            </tr>
        </table>
    @endif
@endsection