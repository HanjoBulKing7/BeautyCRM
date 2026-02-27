{{-- resources/views/layouts/website.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Beauty Bonita')</title>
    
    {{-- Tailwind CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    {{-- Tipografía centralizada --}}
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">

    {{-- Aquí se cargarán los estilos de los módulos --}}
    @stack('styles')
</head>
<body class="font-sans bg-[#eee7df]">
    {{-- Contenido principal --}}
    @yield('content')
    
    {{-- Aquí se cargarán los scripts de los módulos --}}
    @stack('scripts')
</body>
</html>