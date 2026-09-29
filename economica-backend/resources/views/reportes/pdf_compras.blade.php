@extends('reportes.layout')

@section('titulo', 'Reporte General de Compras')

@section('contenido')
    <table class="resumen">
        <tr>
            <td>
                <div class="etq">Compras registradas</div>
                <div class="val">{{ $compras->count() }}</div>
            </td>
            <td>
                <div class="etq">Total invertido</div>
                <div class="val">${{ number_format($granTotal, 2) }}</div>
            </td>
            <td>
                <div class="etq">Compra promedio</div>
                <div class="val">${{ number_format($promedio, 2) }}</div>
            </td>
        </tr>
    </table>

    @forelse($compras as $compra)
        @php $fecha = \Carbon\Carbon::parse($compra->fecha_compra); @endphp
        <div class="bloque">
            <table class="datos">
                <thead>
                    <tr>
                        <th width="14%">Compra</th>
                        <th width="20%">Fecha</th>
                        <th width="16%">Hora</th>
                        <th width="24%" class="centro">Productos</th>
                        <th width="26%" class="derecha">Total compra</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="cab">#{{ $compra->id }}</td>
                        <td class="cab">{{ $fecha->format('d/m/Y') }}</td>
                        <td class="cab">{{ $fecha->format('h:i A') }}</td>
                        <td class="cab centro">{{ $compra->detalles->count() }}</td>
                        <td class="cab derecha">${{ number_format($compra->total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="datos detalle">
                <thead>
                    <tr>
                        <th width="22%">Producto</th>
                        <th width="18%">Proveedor</th>
                        <th width="9%" class="centro">Paquetes</th>
                        <th width="13%" class="centro">Unidades por paquete</th>
                        <th width="12%" class="centro">Total unidades</th>
                        <th width="13%" class="derecha">Precio por paquete</th>
                        <th width="13%" class="derecha">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compra->detalles as $detalle)
                        @php $porPaquete = $detalle->unidades_por_paquete ?: 1; @endphp
                        <tr class="{{ $loop->even ? 'par' : '' }}">
                            <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                            <td>{{ $detalle->proveedor->nombre_proveedor ?? 'Sin proveedor' }}</td>
                            <td class="centro">{{ $detalle->cantidad }}</td>
                            <td class="centro">{{ $porPaquete }}</td>
                            <td class="centro">{{ $detalle->cantidad * $porPaquete }}</td>
                            <td class="derecha">${{ number_format($detalle->precio_compra, 2) }}</td>
                            <td class="derecha">${{ number_format($detalle->subtotal ?? $detalle->cantidad * $detalle->precio_compra, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="vacio">No se encontraron compras en este periodo.</div>
    @endforelse

    @if($compras->count())
        <table class="total-barra">
            <tr>
                <td class="derecha">TOTAL INVERTIDO: ${{ number_format($granTotal, 2) }}</td>
            </tr>
        </table>
    @endif
@endsection