{{-- Historia: H-01 — Consulta de stock disponible (HU01) --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Stock — HegiaStock</title>
    <style>
        /* ── Reset mínimo ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Base: fuente ≥ 16 px, contraste WCAG AA (#1a1a1a sobre #f5f5f5) ── */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            background-color: #f5f5f5;
            color: #1a1a1a;
            padding: 24px 16px;
        }

        /* ── Encabezado ── */
        .site-header {
            background: #1a1a1a;
            color: #ffffff;
            padding: 12px 24px;
            margin: -24px -16px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .site-header h1 { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .site-header .rol { font-size: 13px; color: #cccccc; margin-left: auto; }

        /* ── Contenedor central ── */
        .container { max-width: 820px; margin: 0 auto; }

        /* ── Formulario de búsqueda ── */
        .search-card {
            background: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 4px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }
        .search-card legend {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #555555;
            margin-bottom: 14px;
        }
        .field-group {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex: 1;
            min-width: 180px;
        }
        .field label {
            font-size: 13px;
            font-weight: 700;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .field input[type="text"] {
            border: 1px solid #999999;
            border-radius: 3px;
            padding: 9px 11px;
            font-size: 16px;
            font-family: inherit;
            background: #fafafa;
            color: #1a1a1a;
            width: 100%;
        }
        .field input[type="text"]:focus {
            outline: 3px solid #1a1a1a;
            outline-offset: 1px;
        }
        .btn-buscar {
            background: #1a1a1a;
            color: #ffffff;
            border: 2px solid #1a1a1a;
            border-radius: 3px;
            padding: 9px 22px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }
        .btn-buscar:hover, .btn-buscar:focus {
            background: #444444;
            border-color: #444444;
            outline: none;
        }
        .search-hint {
            font-size: 13px;
            color: #666666;
            margin-top: 8px;
        }

        /* ── Mensaje de aviso ── */
        .aviso {
            background: #f0f0f0;
            border-left: 4px solid #888888;
            border-radius: 0 3px 3px 0;
            padding: 12px 16px;
            font-size: 16px;
            color: #333333;
            margin-bottom: 20px;
        }

        /* ── Ficha de producto ── */
        .producto-card {
            background: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .producto-card-header {
            background: #f0f0f0;
            border-bottom: 1px solid #cccccc;
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #555555;
        }
        .producto-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-bottom: 1px solid #e0e0e0;
        }
        .producto-grid:last-child { border-bottom: none; }
        .celda {
            padding: 14px 20px;
            border-right: 1px solid #e8e8e8;
        }
        .celda:last-child { border-right: none; }
        .celda .etiqueta {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #888888;
            margin-bottom: 4px;
        }
        .celda .valor {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .celda .valor-normal {
            font-size: 15px;
            color: #222222;
        }
        .cantidad-badge {
            display: inline-block;
            background: #e0e0e0;
            border: 1px solid #aaaaaa;
            border-radius: 3px;
            padding: 2px 10px;
            font-size: 17px;
            font-weight: 700;
        }

        /* ── Lista de selección (múltiples coincidencias) ── */
        .lista-titulo {
            font-size: 14px;
            color: #444444;
            margin-bottom: 12px;
        }
        .tabla-coincidencias {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 4px;
            overflow: hidden;
        }
        .tabla-coincidencias thead { background: #ebebeb; }
        .tabla-coincidencias th {
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #555555;
            text-align: left;
            border-bottom: 1px solid #cccccc;
        }
        .tabla-coincidencias td {
            padding: 10px 14px;
            font-size: 15px;
            border-bottom: 1px solid #e8e8e8;
            color: #1a1a1a;
        }
        .tabla-coincidencias tbody tr:last-child td { border-bottom: none; }
        .tabla-coincidencias tbody tr:hover td { background: #f5f5f5; }
        .enlace-detalle {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            text-decoration: underline;
        }
        .enlace-detalle:focus {
            outline: 3px solid #1a1a1a;
            outline-offset: 2px;
        }

        /* ── Pie ── */
        .site-footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #cccccc;
            font-size: 12px;
            color: #888888;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Cabecera --}}
    <header class="site-header" role="banner">
        <h1>HegiaStock</h1>
        <span class="rol">Consulta de Inventario</span>
    </header>

    <main class="container" id="contenido-principal" role="main">

        {{-- ── Formulario de búsqueda ────────────────────────────────────── --}}
        <section aria-label="Búsqueda de producto">
            <form
                action="{{ route('stock.index') }}"
                method="GET"
                role="search"
                aria-label="Buscar producto por SKU o nombre comercial"
            >
                <fieldset class="search-card">
                    <legend>Buscar producto</legend>
                    <div class="field-group">

                        {{-- Campo SKU --}}
                        <div class="field">
                            <label for="campo-sku">
                                SKU
                                <span aria-hidden="true" style="font-weight:400;color:#666"> (código exacto)</span>
                            </label>
                            <input
                                type="text"
                                id="campo-sku"
                                name="sku"
                                value="{{ old('sku', request('sku')) }}"
                                placeholder="Ej: CEM-001"
                                maxlength="50"
                                autocomplete="off"
                                aria-describedby="ayuda-busqueda"
                            >
                        </div>

                        {{-- Campo Nombre --}}
                        <div class="field">
                            <label for="campo-nombre">
                                Nombre comercial
                                <span aria-hidden="true" style="font-weight:400;color:#666"> (parcial)</span>
                            </label>
                            <input
                                type="text"
                                id="campo-nombre"
                                name="q"
                                value="{{ old('q', request('q') ?? request('nombre')) }}"
                                placeholder="Ej: cemento sol"
                                maxlength="150"
                                autocomplete="off"
                                aria-describedby="ayuda-busqueda"
                            >
                        </div>

                        <button type="submit" class="btn-buscar">
                            Consultar stock
                        </button>
                    </div>

                    <p id="ayuda-busqueda" class="search-hint">
                        Ingrese el código SKU exacto <strong>o</strong> parte del nombre comercial del producto.
                        Si busca por SKU y no existe, recibirá un error de recurso no encontrado (404).
                    </p>
                </fieldset>
            </form>
        </section>

        {{-- ── Aviso de error / no encontrado ────────────────────────────── --}}
        @if($aviso)
            <div
                class="aviso"
                role="alert"
                aria-live="polite"
            >
                {{ $aviso }}
            </div>
        @endif

        {{-- ── Modo DETALLE: un solo producto encontrado ──────────────────── --}}
        @if($modo === 'detalle' && $producto)
            <section aria-label="Ficha del producto">
                <div class="producto-card">
                    <div class="producto-card-header">
                        Resultado — stock disponible
                    </div>

                    {{-- Fila 1: SKU · Nombre · Categoría --}}
                    <div class="producto-grid">
                        <div class="celda">
                            <div class="etiqueta">SKU</div>
                            <div class="valor">{{ $producto->sku }}</div>
                        </div>
                        <div class="celda">
                            <div class="etiqueta">Nombre Comercial</div>
                            <div class="valor-normal">{{ $producto->nombre_comercial }}</div>
                        </div>
                        <div class="celda">
                            <div class="etiqueta">Categoría</div>
                            <div class="valor-normal">
                                {{-- La relación puede ser null si el JOIN falla; mostrar aviso --}}
                                {{ $producto->categoria?->nombre ?? '— sin categoría —' }}
                            </div>
                        </div>
                    </div>

                    {{-- Fila 2: Cantidad disponible · Unidad --}}
                    <div class="producto-grid">
                        <div class="celda">
                            <div class="etiqueta">Cantidad disponible</div>
                            <div class="valor">
                                <span class="cantidad-badge" aria-label="{{ $producto->cantidad_disponible }} unidades disponibles">
                                    {{ $producto->cantidad_disponible }}
                                </span>
                            </div>
                        </div>
                        <div class="celda">
                            <div class="etiqueta">Unidad de medida</div>
                            <div class="valor-normal">
                                {{ $producto->unidadMedida?->simbolo ?? '—' }}
                                @if($producto->unidadMedida?->descripcion)
                                    <span style="font-size:13px;color:#666">
                                        ({{ $producto->unidadMedida->descripcion }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="celda">
                            <div class="etiqueta">Estado</div>
                            <div class="valor-normal">
                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ── Modo LISTA: múltiples coincidencias (HU01 CA3) ─────────────── --}}
        @if($modo === 'lista' && count($coincidencias) > 0)
            <section aria-label="Lista de productos coincidentes">
                <p class="lista-titulo" role="status" aria-live="polite">
                    Se encontraron <strong>{{ count($coincidencias) }} productos</strong>
                    con «{{ $termino }}». Seleccione el producto que desea consultar:
                </p>

                <table class="tabla-coincidencias" aria-label="Productos coincidentes">
                    <thead>
                        <tr>
                            <th scope="col">SKU</th>
                            <th scope="col">Nombre Comercial</th>
                            <th scope="col">Categoría</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Unidad</th>
                            <th scope="col"><span class="sr-only">Acción</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coincidencias as $item)
                        <tr>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->nombre_comercial }}</td>
                            <td>{{ $item->categoria?->nombre ?? '—' }}</td>
                            <td>{{ $item->cantidad_disponible }}</td>
                            <td>{{ $item->unidadMedida?->simbolo ?? '—' }}</td>
                            <td>
                                <a
                                    href="{{ route('stock.index', ['sku' => $item->sku]) }}"
                                    class="enlace-detalle"
                                    aria-label="Ver detalle de {{ $item->nombre_comercial }}"
                                >
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

    </main>

    <footer class="site-footer" role="contentinfo">
        HegiaStock &mdash; Inversiones Hegiapesa S.A.C. &nbsp;|&nbsp; Unidad 2
    </footer>

</body>
</html>
