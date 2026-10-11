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
        <input
            type="text"
            id="sku"
            name="sku"
            value="{{ request('sku') }}"
            required
        >
        <button type="submit">Consultar</button>
    </form>

    @isset($ubicacion)
        <h2>Resultado</h2>
        <p>Producto: {{ $producto->sku }}</p>
        <p>{{ $ubicacion }}</p>
    @endisset
</body>
</html>
