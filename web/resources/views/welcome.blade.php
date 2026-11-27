@extends('layouts.app')

@section('title', 'Panel de control')

@section('content')

@php
    $productos = \Illuminate\Support\Facades\DB::table('producto')
        ->where('activo', 1);

    $total = (clone $productos)->count();

    $disponibles = (clone $productos)
        ->where('cantidad_disponible', '>', 0)->count();

    $bajo = (clone $productos)
        ->whereBetween('cantidad_disponible', [1, 5])->count();

    $agotados = (clone $productos)
        ->where('cantidad_disponible', 0)->count();

    $inventario = (clone $productos)
        ->orderBy('sku')->get();
@endphp

<h1>Panel de control</h1>

<p class="subtitle">
    Bienvenido a HegiaStock, sistema inteligente de inventario.
</p>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-label">Productos registrados activos</div>
        <div class="stat-number blue">{{ $total }}</div>
        <small>Catálogo de inventario</small>
    </div>

    <div class="stat-card">
        <div class="stat-label">Productos disponibles</div>
        <div class="stat-number green">{{ $disponibles }}</div>
        <small>Con existencias</small>
    </div>

    <div class="stat-card">
        <div class="stat-label">Stock bajo</div>
        <div class="stat-number orange">{{ $bajo }}</div>
        <small>Requieren reposición</small>
    </div>

    <div class="stat-card">
        <div class="stat-label">Productos agotados</div>
        <div class="stat-number red">{{ $agotados }}</div>
        <small>Sin existencias</small>
    </div>

</div>

<div class="panel" style="margin-bottom:25px">

    <h2>Accesos rápidos</h2>

    <p class="subtitle">
        Consulta la información de tu almacén.
    </p>

    <div style="display:flex;gap:12px;flex-wrap:wrap">

        <a href="{{ url('/stock') }}" class="btn">
            Consultar stock
        </a>

        <a href="{{ url('/ubicacion') }}" class="btn">
            Buscar ubicación
        </a>

    </div>

</div>

<div class="panel">

    <h2>Inventario general</h2>

    <p class="subtitle">
        Información actualizada desde la base de datos.
    </p>

    <div class="table-responsive">

        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th>Cantidad disponible</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                @forelse($inventario as $producto)

                    <tr>
                        <td>{{ $producto->sku }}</td>

                        <td>
                            {{ $producto->nombre_comercial }}
                        </td>

                        <td>
                            {{ $producto->cantidad_disponible }}
                        </td>

                        <td>

                            @if($producto->cantidad_disponible == 0)

                                <span class="status status-empty">
                                    Agotado
                                </span>

                            @elseif($producto->cantidad_disponible <= 5)

                                <span class="status status-low">
                                    Stock bajo
                                </span>

                            @else

                                <span class="status status-ok">
                                    Disponible
                                </span>

                            @endif

                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="4">
                            No hay productos registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>

    </div>

</div>

@endsection