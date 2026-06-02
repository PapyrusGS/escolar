<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#060912">
    <title>@yield('title', 'Sistema Escolar')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    <div id="@yield('mount-id', 'app')"></div>
    <div id="toast-stack"></div>
    @stack('body')
</body>
</html>
