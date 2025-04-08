<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel')</title>
    @vite(['resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
    @yield('content')
</body>
</html>
