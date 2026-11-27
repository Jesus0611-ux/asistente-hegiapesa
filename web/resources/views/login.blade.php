
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | HegiaStock</title>

    <link rel="stylesheet"
          href="{{ asset('css/hegiastock.css') }}">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a, #1e3a5f);
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 410px;
        }

        .login-brand {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .login-brand h1 {
            font-size: 38px;
        }

        .login-brand span {
            color: #60a5fa;
        }

        .login-brand p {
            color: #94a3b8;
        }

        .login-card {
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(0,0,0,.2);
        }

        .login-card input {
            width: 100%;
            margin: 8px 0 20px;
        }

        .login-card button {
            width: 100%;
        }

        .login-footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-brand">
        <h1>Hegia<span>Stock</span></h1>
        <p>Gestión inteligente de inventario</p>
    </div>

    <div class="login-card">

        <h2 style="margin-top:0">Bienvenido</h2>

        <p class="subtitle">
            Ingresa tus credenciales para continuar.
        </p>

        @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;
                        padding:12px;border-radius:8px;
                        margin-bottom:20px">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">

            @csrf

            <label for="usuario">Nombre de usuario</label>

            <input type="text"
                   id="usuario"
                   name="nombre_usuario"
                   placeholder="Ingresa tu usuario"
                   autocomplete="username"
                   required autofocus>

            <label for="password">Contraseña</label>

            <input type="password"
                   id="password"
                   name="password"
                   placeholder="Ingresa tu contraseña"
                   autocomplete="current-password"
                   required>

            <button type="submit">
                Iniciar sesión
            </button>

        </form>

        <p style="text-align:center;color:#64748b;
                  font-size:12px;margin-top:25px">
            Acceso exclusivo para personal autorizado
        </p>

    </div>

    <div class="login-footer">
        Inversiones Hegiapesa S.A.C. · 2026
    </div>

</div>

</body>
</html>
