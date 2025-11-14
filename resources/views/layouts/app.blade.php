<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beauty Bonita')</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fuente Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        .font-montas { font-family: 'Montserrat', sans-serif; }
        .sidebar-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo y nombre -->
                <div class="flex items-center">
                    <a href="{{ Auth::user()->role_id == 3 ? url('/admin/home') : url('/home') }}">
                        <img src="{{ asset('iconos/logo blanco.png') }}" alt="Logo" class="h-8 mr-3">
                    </a>
                    <span class="text-lg font-medium font-montas">Beauty Bonita Dashboard</span>
                </div>
                
                <!-- Menú principal desktop -->
                <div class="hidden md:flex items-center space-x-1">
                    @if(Auth::user()->role_id == 3)
                        <!-- Menú Admin -->
                        <a href="{{ url('/admin/home') }}" class="nav-item px-4 py-2 flex items-center hover:bg-white hover:bg-opacity-20 rounded-lg transition">
                            <i class="fas fa-home mr-2 w-5 h-5"></i>Dashboard
                        </a>
                    @else
                        <!-- Menú Cliente -->
                        <a href="{{ url('/home') }}" class="nav-item px-4 py-2 flex items-center hover:bg-white hover:bg-opacity-20 rounded-lg transition">
                            <i class="fas fa-home mr-2 w-5 h-5"></i>Inicio
                        </a>
                    @endif
                </div>
                
                <!-- Usuario y acciones -->
                <div class="flex items-center">
                    <button class="p-2 rounded-full hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="ml-4 flex items-center">
                        <img src="{{ asset('images/cejas.png') }}" alt="Usuario" class="h-8 w-8 rounded-full">
                        <span class="ml-2 text-sm font-medium">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile menu button -->
    <div class="md:hidden flex justify-between items-center p-4 bg-white border-b">
        <button id="mobile-menu-button" class="text-gray-500">
            <i class="fas fa-bars"></i>
        </button>
        <img src="{{ asset('iconos/logo.png') }}" alt="Logo" class="h-6">
        <div class="w-6"></div>
    </div>

    <!-- Main Layout -->
    <div class="flex h-screen pt-16">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-white w-64 shadow-lg sidebar-transition transform -translate-x-full md:translate-x-0 fixed md:relative h-full z-40">
            <div class="p-4">
                @if(Auth::user()->role_id == 3)
                    @include('components.sidebar-admin')
                @else
                    @include('components.sidebar-cliente')
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden hidden"></div>

    @yield('scripts')

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        document.getElementById('mobile-overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>