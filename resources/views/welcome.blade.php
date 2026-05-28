<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso al Sistema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255, 179, 71, 0.35), transparent 28%),
                linear-gradient(135deg, #0f172a, #111827 55%, #1f2937);
            color: #e5e7eb;
        }

        .shell {
            width: min(92vw, 420px);
            padding: 2rem;
            border-radius: 24px;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(16px);
        }

        .title {
            margin: 0 0 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .subtitle {
            margin: 0 0 1.5rem;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <main class="shell">
        <h1 class="title">Sistema Escolar</h1>
        <p class="subtitle">Inicia sesión con tu Correo y Contrasena.</p>
        <div id="login-app"></div>
    </main>
</body>
</html>
