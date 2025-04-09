<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Navbar global --}}
    @include('components.navbar')

    <div class="container w-100 full-height-dynamic">

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div id="flash-success" class="alert alert-success alert-dismissible fade show text-center mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Mensaje de error específico de backup --}}
        @if ($errors->has('backup'))
            <div id="flash-error" class="alert alert-danger alert-dismissible fade show text-center mt-3" role="alert">
                {{ $errors->first('backup') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Contenido de la vista --}}
        @yield('content')
    </div>

    {{-- Script para ocultar los mensajes automáticamente después de 4 segundos --}}
    <script>
        setTimeout(() => {
            const success = document.getElementById('flash-success');
            const error = document.getElementById('flash-error');

            if (success) success.classList.remove('show');
            if (error) error.classList.remove('show');
        }, 6000);
    </script>
</body>
</html>

