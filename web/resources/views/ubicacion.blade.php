{{-- Historia: H-02 --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de ubicación</title>
</head>
<body>
    <h1>Consulta de ubicación de producto</h1>

    <form action="{{ url('/ubicacion') }}" method="GET">
        <label for="sku">Código SKU:</label>
        <input type="text" id="sku" name="sku" value="{{ request('sku') }}">
        <label for="q">Nombre comercial:</label>
        <input
            type="text"
            id="q"
            name="q"
            value="{{ request('q', request('nombre')) }}"
        >
        <button type="submit">Consultar</button>
    </form>

    @if (!empty($aviso))
        <p role="status">{{ $aviso }}</p>
    @endif

    @if (($modo ?? null) === 'lista')
        <h2>Seleccione el producto</h2>
        <ul>
            @foreach ($coincidencias as $coincidencia)
                <li>
                    <form action="{{ url('/ubicacion') }}" method="GET">
                        <input type="hidden" name="sku" value="{{ $coincidencia->sku }}">
                        <button type="submit">
                            {{ $coincidencia->sku }} - {{ $coincidencia->nombre_comercial }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    @if (($modo ?? null) === 'detalle' && $producto)
        <h2>Resultado</h2>
        <p>Producto: {{ $producto->sku }} — {{ $producto->nombre_comercial }}</p>
        @if ($ubicacion)
            <p>{{ $ubicacion }}</p>
        @endif
    @endif
</body>
</html>
