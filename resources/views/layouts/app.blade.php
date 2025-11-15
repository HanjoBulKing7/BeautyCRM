<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beauty Bonita')</title>
    
    <!-- Tailwind CSS via CDN (como estaba en Beauty Bonita original) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- TODO EL CSS PERSONALIZADO DEL CRM COPIADO DIRECTAMENTE -->
    <style>
        /* ================== SIDEBAR FIJO Y RESPONSIVO ================== */
        .sidebar {
          position: fixed;
          top: 0;
          left: 0;
          height: 100vh;
          width: 16rem;
          background-color: #4b5563; /* Gris medio */
          border-right: 1px solid #6b7280;
          overflow-y: auto;
          flex-shrink: 0;
          transition: all 0.3s ease;
          z-index: 40;
        }

        .sidebar::-webkit-scrollbar {
          width: 6px;
        }
        .sidebar::-webkit-scrollbar-thumb {
          background-color: rgba(255, 255, 255, 0.2);
          border-radius: 3px;
        }

        /* ===== Overlay para móviles ===== */
        .overlay {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: rgba(0, 0, 0, 0.5);
          z-index: 30;
        }

        /* ===== Contenido principal ===== */
        main {
          margin-left: 16rem;
          transition: margin-left 0.3s ease;
        }

        /* ===== Header fijo ===== */
        header {
          position: fixed;
          top: 0;
          left: 16rem;
          right: 0;
          height: 4.5rem;
          background-color: #fff;
          z-index: 50;
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 2rem;
          border-bottom: 1px solid #e5e7eb;
          transition: left 0.3s ease;
        }

        /* ===== Ajuste general del body ===== */
        body {
          overflow-x: hidden;
        }

        /* ================== FIX PARA MÓVILES ================== */
        @media (max-width: 1024px) {
          .sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 50;
            height: 100vh;
            overflow-y: scroll;
            padding-bottom: 5rem;
          }

          .sidebar.open {
            transform: translateX(0);
          }

          .overlay.open {
            display: block;
          }

          main {
            margin-left: 0 !important;
            padding: 1rem;
          }

          .ml-64 {
            margin-left: 0 !important;
          }

          header {
            left: 0 !important;
            width: 100% !important;
            padding: 0 1rem;
          }

          /* Fija el bloque inferior del sidebar (usuario + salir) */
          .sidebar .p-4.border-t {
            position: sticky;
            bottom: 0;
            z-index: 60;
            background-color: #4b5563;
            border-top: 1px solid #6b7280;
          }
        }

        /* ===== PEQUEÑO EFECTO AL HOVER EN SIDEBAR ===== */
        .sidebar a:hover {
          transform: scale(1.02);
          transition: transform 0.2s ease;
        }

        /* Estilos para texto blanco en sidebar */
        .sidebar,
        .sidebar * {
          color: white !important;
        }

        .sidebar .text-gray-800,
        .sidebar .text-gray-700,
        .sidebar .text-gray-600,
        .sidebar .text-gray-500 {
          color: white !important;
        }

        /* Efectos hover para sidebar gris */
        .sidebar a:hover {
          background-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Estados activos para sidebar gris */
        .sidebar a.bg-gray-200,
        .sidebar a.bg-orange-100,
        .sidebar a.bg-yellow-100,
        .sidebar a.bg-blue-100,
        .sidebar a.bg-purple-100,
        .sidebar a.bg-red-100,
        .sidebar a.bg-pink-100,
        .sidebar a.bg-teal-100,
        .sidebar a.bg-green-50 {
          background-color: rgba(255, 255, 255, 0.2) !important;
          color: white !important;
        }

        /* Íconos en sidebar gris */
        .sidebar .text-orange-500,
        .sidebar .text-yellow-500,
        .sidebar .text-blue-500,
        .sidebar .text-purple-500,
        .sidebar .text-red-500,
        .sidebar .text-pink-500,
        .sidebar .text-teal-500,
        .sidebar .text-green-500,
        .sidebar .text-gray-600 {
          color: #d1d5db !important;
        }

        /* Estilos específicos para el botón de tema */
        .dark-mode #theme-toggle {
            background-color: #374151 !important;
        }

        .dark-mode #theme-toggle:hover {
            background-color: #4b5563 !important;
        }

        .dark-mode #theme-icon {
            color: #d1d5db !important;
        }

        /* Asegurar que el sol sea amarillo en ambos temas */
        #theme-icon.fa-sun {
            color: #f59e0b !important;
        }

        .dark-mode #theme-icon.fa-sun {
            color: #f59e0b !important;
        }

        /* Asegurar que la luna sea visible en dark mode */
        .dark-mode #theme-icon.fa-moon {
            color: #d1d5db !important;
        }

        /* Estilos para el tema oscuro */
        .dark-mode {
            background-color: #1f2937;
            color: #f9fafb;
        }

        .dark-mode .bg-white {
            background-color: #374151 !important;
        }

        .dark-mode .text-gray-800 {
            color: #f9fafb !important;
        }

        .dark-mode .text-gray-700 {
            color: #e5e7eb !important;
        }

        .dark-mode .text-gray-500 {
            color: #9ca3af !important;
        }

        .dark-mode .border-gray-200 {
            border-color: #4b5563 !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex transition-colors duration-300">
<div id="overlay" class="overlay"></div>

<!-- Sidebar - CAMBIAR bg-white por bg-gray-600 -->
<nav id="sidebar" class="sidebar bg-gray-600 shadow w-64 flex-shrink-0 flex flex-col justify-between fixed left-0 top-0 h-screen overflow-y-auto z-40">
    <div>
        <!-- Logo -->
        <div class="p-4 border-b border-gray-500 flex items-center justify-center">
            <img src="{{ asset('iconos/logo blanco.png') }}" alt="Logo" class="h-12">
        </div>

        <!-- Links del Sidebar (Condicional por Rol) -->
        <div class="p-4">
            @if(Auth::user()->role_id == 3)
                @include('components.sidebar-admin')
            @else
                @include('components.sidebar-cliente')
            @endif
        </div>
    </div>

    <!-- Usuario + Logout -->
    <div class="p-4 border-t border-gray-500">
        <div class="flex items-center space-x-3 mb-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-500 text-white font-bold text-lg">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div>
                <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-300 capitalize">
                    {{ Auth::user()->role_id == 3 ? 'Administrador' : 'Cliente' }}
                </p>
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

<!-- HEADER ACTUALIZADO CON TEMA OSCURO Y NOTIFICACIONES -->
<header class="bg-white border border-gray-200 shadow-md flex items-center justify-between px-6 py-4 fixed top-0 left-64 right-0 z-30 transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center space-x-3">
        <button id="sidebar-toggle" class="md:hidden p-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors dark:bg-gray-700 dark:hover:bg-gray-600">
            <i class="fas fa-bars text-gray-700 text-lg dark:text-gray-300"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Modo oscuro -->
        <button id="theme-toggle" class="p-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors dark:bg-gray-700 dark:hover:bg-gray-600">
            <i class="fas fa-moon text-gray-700 dark:text-gray-300" id="theme-icon"></i>
        </button>

        <!-- Notificaciones -->
        <div class="relative">
            <button id="notifications-toggle"
                class="p-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 transition-colors text-white relative dark:bg-yellow-600 dark:hover:bg-yellow-700">
                <i class="fas fa-bell"></i>
                <span id="notification-count"
                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center hidden">0</span>
            </button>

            <!-- Panel de notificaciones -->
            <div id="notifications-panel"
                class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 shadow-md rounded-xl z-50 hidden max-h-96 overflow-y-auto dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Notificaciones</h3>
                </div>
                <div id="notifications-list" class="p-2">
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando notificaciones...
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main content -->
<main class="flex-1 p-4 ml-64 mt-20 overflow-y-auto">
    <div class="max-w-6xl mx-auto">
        @if(session('ok'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </div>
</main>

<!-- JavaScript para producción -->
@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
@endphp

@if(isset($manifest['resources/js/app.js']))
<script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
@endif

<script>
// ==================== REFERENCIAS GLOBALES ====================
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const sidebarToggle = document.getElementById('sidebar-toggle');
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const notificationsToggle = document.getElementById('notifications-toggle');
const notificationsPanel = document.getElementById('notifications-panel');
const body = document.body;

// ==================== TOGGLE SIDEBAR EN PANTALLAS PEQUEÑAS ====================
sidebarToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
});

overlay?.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
});

// ==================== TOGGLE TEMA OSCURO ====================
function toggleTheme() {
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    
    if (isDark) {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
    }
    
    localStorage.setItem('darkMode', isDark);
}

// Inicializar tema
const savedTheme = localStorage.getItem('darkMode');
if (savedTheme === 'true') {
    body.classList.add('dark-mode');
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
}

themeToggle?.addEventListener('click', toggleTheme);

// ==================== NOTIFICACIONES ====================
notificationsToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    notificationsPanel.classList.toggle('hidden');
});

// Cerrar notificaciones al hacer clic fuera
document.addEventListener('click', (e) => {
    if (!notificationsToggle?.contains(e.target) && !notificationsPanel?.contains(e.target)) {
        notificationsPanel?.classList.add('hidden');
    }
});

// Función para cargar notificaciones (puedes personalizar esto)
function loadNotifications() {
    // Aquí puedes hacer una petición AJAX para cargar notificaciones reales
    const notificationsList = document.getElementById('notifications-list');
    if (notificationsList) {
        notificationsList.innerHTML = `
            <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                <p class="text-sm text-gray-700 dark:text-gray-300">No hay notificaciones nuevas</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Todo está al día</p>
            </div>
        `;
    }
}

// Cargar notificaciones al abrir el panel
notificationsToggle?.addEventListener('click', loadNotifications);
</script>

@yield('scripts')
</body>
</html>