<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Principal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(251, 191, 36, 0.22), transparent 26%),
                linear-gradient(135deg, #0f172a, #111827 55%, #1f2937);
        }

        .shell {
            width: min(96vw, 1100px);
            margin: 0 auto;
            padding: 2rem 1rem;
        }
    </style>
</head>
<body>
    <main class="shell">
        <div id="index-app"></div>
    </main>
</body>
</html>
