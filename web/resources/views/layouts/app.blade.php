
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Inicio') | HegiaStock</title>

    <link rel="stylesheet"
          href="{{ asset('css/hegiastock.css') }}">
</head>

<body>

    <aside class="sidebar">

        <div class="brand">
            Hegia<span>Stock</span>
        </div>

        <div class="brand-description">
            Gestión inteligente de inventario
        </div>

        <div class="menu-label">MENÚ PRINCIPAL</div>

        <nav>
            <a href="{{ url('/') }}"
               class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                ◈ &nbsp; Panel de control
            </a>

            <a href="{{ url('/stock') }}"
               class="nav-link {{ request()->is('stock') ? 'active' : '' }}">
                ▦ &nbsp; Consultar stock
            </a>

            <a href="{{ url('/ubicacion') }}"
               class="nav-link {{ request()->is('ubicacion') ? 'active' : '' }}">
                ◎ &nbsp; Ubicaciones
            </a>
        </nav>

        <div class="sidebar-footer">
            Inversiones Hegiapesa S.A.C.<br>
            Sistema de inventario · 2026
        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <span>Gestión de almacén</span>
            <div style="display:flex;align-items:center;gap:15px">

    <span>
        {{ auth()->user()->nombre_usuario }}
    </span>

    <form method="POST" action="{{ route('logout') }}">

        @csrf

        <button type="submit"
                style="background:#e2e8f0;color:#334155;
                       padding:9px 15px">
            Cerrar sesión
        </button>

    </form>

</div>
        </header>

        <section class="content">
            @yield('content')
        </section>

    </main>

@auth
    @include('components.asistente')
@endauth

</body>
</html>
