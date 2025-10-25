<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex">

<!-- Sidebar -->
<nav class="sidebar bg-white shadow-lg w-64 flex-shrink-0 flex flex-col justify-between fixed left-0 top-0 h-screen overflow-y-auto z-40">
    <div>
        <!-- Logo -->
        <div class="p-4 border-b flex items-center justify-center">
            <img src="{{ asset('img/elprogreso.png') }}" alt="Logo" class="h-12">
        </div>

        <!-- Links -->
        <div class="p-4">
           <ul class="space-y-3">
    <!-- Inicio -->
    <li>
        <a href="{{ route('dashboard') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('dashboard') ? 'bg-orange-100 text-orange-600 font-semibold' : 'text-gray-700 hover:bg-orange-50' }}">
            <i class="fas fa-home mr-4 text-orange-500 text-xl"></i>
            <span>Inicio</span>
        </a>
    </li>

    <!-- Productos -->
    <li>
        <a href="{{ route('productos.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('productos.*') ? 'bg-yellow-100 text-yellow-600 font-semibold' : 'text-gray-700 hover:bg-yellow-50' }}">
            <i class="fas fa-cookie-bite mr-4 text-yellow-500 text-xl"></i>
            <span>Productos</span>
        </a>
    </li>

    <!-- Inventario -->
    <li>
        <a href="{{ route('inventario.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('inventario.*') ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700 hover:bg-blue-50' }}">
            <i class="fas fa-boxes-stacked mr-4 text-blue-500 text-xl"></i>
            <span>Inventario</span>
        </a>
    </li>

    <!-- Empleados -->
    <li>
        <a href="{{ route('empleados.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('empleados.*') ? 'bg-purple-100 text-purple-600 font-semibold' : 'text-gray-700 hover:bg-purple-50' }}">
            <i class="fas fa-users mr-4 text-purple-500 text-xl"></i>
            <span>Empleados</span>
        </a>
    </li>

    <!-- ✅ NUEVO: Rutas -->
    <li>
        <a href="{{ route('rutas.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('rutas.*') ? 'bg-orange-100 text-orange-600 font-semibold' : 'text-gray-700 hover:bg-orange-50' }}">
            <i class="fas fa-truck mr-4 text-orange-500 text-xl"></i>
            <span>Rutas</span>
        </a>
    </li>

    <!-- Ventas -->
    <li>
        <a href="{{ route('ventas.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('ventas.*') ? 'bg-red-100 text-red-600 font-semibold' : 'text-gray-700 hover:bg-red-50' }}">
            <i class="fas fa-cash-register mr-4 text-red-500 text-xl"></i>
            <span>Ventas</span>
        </a>
    </li>

    <!-- Gastos -->
    <li>
        <a href="{{ route('gastos.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('gastos.*') ? 'bg-green-100 text-green-600 font-semibold' : 'text-gray-700 hover:bg-green-50' }}">
            <i class="fas fa-money-bill-wave mr-4 text-green-500 text-xl"></i>
            <span>Gastos</span>
        </a>
    </li>

    <!-- Reportes -->
    <li>
        <a href="{{ route('reportes.index') }}"
           class="flex items-center p-3 rounded-lg text-lg transition duration-200 hover:scale-105
                  {{ request()->routeIs('reportes.*') ? 'bg-pink-100 text-pink-600 font-semibold' : 'text-gray-700 hover:bg-pink-50' }}">
            <i class="fas fa-chart-line mr-4 text-pink-500 text-xl"></i>
            <span>Reportes</span>
        </a>
    </li>
</ul>

        </div>
    </div>

    <!-- Usuario + Logout -->
    <div class="p-4 border-t">
        <div class="flex items-center space-x-3 mb-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-500 text-white font-bold text-lg">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 2)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ Auth::user()->nombre }}</p>
                <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->rol }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center p-2 text-white bg-red-500 rounded-lg hover:bg-red-600 transition duration-200 hover:scale-105">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span>Salir</span>
            </button>
        </form>
    </div>
</nav>

<!-- Contenido principal -->
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow mb-6 p-4 flex items-center">
        <button id="menu-toggle" class="md:hidden mr-4 text-gray-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
    </header>

    <!-- Main content -->
    <main class="flex-1 p-4 ml-64 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            @if(session('ok'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('ok') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
