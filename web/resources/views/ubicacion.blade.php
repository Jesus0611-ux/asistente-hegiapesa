
@extends('layouts.app')

@section('title', 'Ubicaciones')

@section('content')

<h1>Ubicación de productos</h1>

<p class="subtitle">
    Localiza los productos dentro del almacén de Hegiapesa.
</p>

<div class="panel" style="margin-bottom:25px">

    <h2>Localizar producto</h2>

    <form action="{{ url('/ubicacion') }}" method="GET">

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
                       value="{{ request('q', request('nombre')) }}"
                       placeholder="Ej: Cemento"
                       style="width:100%;margin-top:8px">
            </div>

            <button type="submit">Buscar ubicación</button>

        </div>

    </form>

</div>

@if(!empty($aviso))

    <div class="panel" style="margin-bottom:20px;border-left:4px solid #d97706">
        {{ $aviso }}
    </div>

@endif

@if(($modo ?? null) === 'lista')

    <div class="panel">

        <h2>Productos encontrados</h2>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($coincidencias as $coincidencia)

                        <tr>
                            <td>{{ $coincidencia->sku }}</td>
                            <td>{{ $coincidencia->nombre_comercial }}</td>
                            <td>
                                <a class="blue"
                                   href="{{ url('/ubicacion') }}?sku={{ urlencode($coincidencia->sku) }}">
                                    Ver ubicación
                                </a>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@endif

@if(($modo ?? null) === 'detalle' && $producto)

    <div class="panel">

        <span class="status status-ok">Producto encontrado</span>

        <h2>{{ $producto->nombre_comercial }}</h2>

        <p class="subtitle">SKU: {{ $producto->sku }}</p>

        <div style="background:#eff6ff;padding:25px;border-radius:12px;border:1px solid #bfdbfe">

            <div class="stat-label">Ubicación en el almacén</div>

            <h2 class="blue">
                {{ $ubicacion ?? 'Ubicación no registrada' }}
            </h2>

        </div>

        <br>

        <a class="btn"
           href="{{ route('stock.index', ['sku' => $producto->sku]) }}">
            Consultar stock
        </a>

    </div>

@endif

@endsection
