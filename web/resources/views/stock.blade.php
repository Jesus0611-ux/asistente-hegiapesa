
@extends('layouts.app')

@section('title', 'Consultar stock')

@section('content')

<h1>Consulta de stock</h1>

<p class="subtitle">
    Consulta las existencias de los productos del almacén.
</p>

<div class="panel" style="margin-bottom:25px">

    <h2>Buscar producto</h2>

    <form action="{{ route('stock.index') }}" method="GET">

        <div style="display:flex;gap:15px;flex-wrap:wrap;align-items:end">

            <div style="flex:1">
                <label>Código SKU</label><br>
                <input type="text" name="sku"
                       value="{{ request('sku') }}"
                       placeholder="Ej: HEG-001"
                       style="width:100%;margin-top:8px">
            </div>

            <div style="flex:1">
                <label>Nombre comercial</label><br>
                <input type="text" name="q"
                       value="{{ request('q') }}"
                       placeholder="Ej: Cemento"
                       style="width:100%;margin-top:8px">
            </div>

            <button type="submit">Buscar producto</button>

        </div>

    </form>

</div>

@if(!empty($aviso))
    <div class="panel" style="margin-bottom:20px;border-left:4px solid #d97706">
        {{ $aviso }}
    </div>
@endif

@if($modo === 'detalle' && $producto)

    <div class="panel">

        <h2>Información del producto</h2>

        <p class="subtitle">Resultado de la consulta</p>

        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-label">Código SKU</div>
                <h2>{{ $producto->sku }}</h2>
            </div>

            <div class="stat-card">
                <div class="stat-label">Producto</div>
                <h2>{{ $producto->commercialName }}</h2>
            </div>

            <div class="stat-card">
                <div class="stat-label">Cantidad disponible</div>
                <div class="stat-number blue">
                    {{ $producto->availableQuantity }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Estado</div>
                <br>

                @if($producto->availableQuantity == 0)
                    <span class="status status-empty">Agotado</span>
                @elseif($producto->availableQuantity <= 5)
                    <span class="status status-low">Stock bajo</span>
                @else
                    <span class="status status-ok">Disponible</span>
                @endif

            </div>

        </div>

        <a class="btn"
           href="{{ url('/ubicacion') }}?sku={{ urlencode($producto->sku) }}">
            Consultar ubicación
        </a>

    </div>

@endif

@if($modo === 'lista' && count($coincidencias) > 0)

    <div class="panel">

        <h2>Productos encontrados</h2>

        <p class="subtitle">
            Se encontraron {{ count($coincidencias) }} coincidencias.
        </p>

        <div class="table-responsive">

            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($coincidencias as $item)

                        <tr>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->commercialName }}</td>
                            <td>{{ $item->availableQuantity }}</td>
                            <td>
                                <a class="blue"
                                   href="{{ route('stock.index', ['sku' => $item->sku]) }}">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>

                    @endforeach

                </tbody>
            </table>

        </div>

    </div>

@endif

@endsection
