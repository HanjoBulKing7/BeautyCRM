{{-- resources/views/layouts/website.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- ==========================================
         SEO & Meta Tags
       ========================================== --}}
    <title>@yield('title', 'Beauty Studio - Salón de Belleza en Aguascalientes')</title>
    <meta name="description" content="Encuentra los mejores servicios de belleza en Aguascalientes. Expertos en uñas acrílicas, extensiones de pestañas, microblading, balayage y maquillaje profesional. ¡Agenda tu cita hoy!">
    <meta name="keywords" content="estudio de belleza en Aguascalientes, salón de belleza Aguascalientes, uñas acrílicas, extensiones de pestañas, microblading, balayage, maquillaje profesional, manicura y pedicura, diseño de cejas, alaciado permanente, agendar cita de belleza">
    
    {{-- URL Canónica (Evita penalizaciones de Google por contenido duplicado) --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ==========================================
         Open Graph (Tarjetas para WhatsApp y Redes)
       ========================================== --}}
    <meta property="og:title" content="@yield('title', 'Beauty Studio - Salón de Belleza en Aguascalientes')">
    <meta property="og:description" content="Expertos en uñas acrílicas, pestañas, maquillaje y más en Aguascalientes. ¡Agenda tu cita hoy!">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    {{-- Nota: Sube una imagen bonita de tu estudio de 1200x630px en public/images/ --}}
    <meta property="og:image" content="{{ asset('images/vista-previa-social.jpg') }}">

    {{-- ==========================================
         Archivos CSS, Favicon y Fuentes
       ========================================== --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}?v=2">
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