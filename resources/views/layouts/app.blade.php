<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Beauty Bonita')</title>

  <!-- ✅ CSS/JS (build si existe) + fallback -->
  @php
      $manifestPath = public_path('build/manifest.json');
      $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
  @endphp

  {{-- ✅ APP.CSS --}}
  @if(isset($manifest['resources/css/app.css']))
    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
  @else
    {{-- Fallback: Tailwind CDN (solo si NO hay build) --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    {{-- (Opcional) si copiaste componentes a public/css/components.css --}}
    @if(file_exists(public_path('css/components.css')))
      <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @endif

    {{-- ✅ (Opcional) si copiaste ui.css a public/css/ui.css --}}
    @if(file_exists(public_path('css/ui.css')))
      <link rel="stylesheet" href="{{ asset('css/ui.css') }}">
    @endif
  @endif

  {{-- ✅ COMPONENTS.CSS (dark-mode y overrides) --}}
  @if(isset($manifest['resources/css/components.css']))
    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/components.css']['file']) }}">
  @endif

  {{-- ✅ UI.CSS (bb-* design system para tus vistas index) --}}
  @if(isset($manifest['resources/css/ui.css']))
    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/ui.css']['file']) }}">
  @endif

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- 🔹 Permite inyectar estilos desde las vistas -->
  @stack('styles')

  <!-- ✅ THEME: Light (Glass blanco + dorado). Dark mode se va a resources/css/components.css -->
  <style>
    :root{
      --bb-gold: #C9A24A;
      --bb-gold-2: #E7D7A1;
      --bb-ink: #111827;
      --bb-muted: #6B7280;
      --bb-border: rgba(17,24,39,.10);
      --bb-glass: rgba(255,255,255,.72);
      --bb-glass-strong: rgba(255,255,255,.85);
      --bb-shadow: 0 10px 30px rgba(17,24,39,.08);
    }

    body{
      overflow-x:hidden;
      color: var(--bb-ink);
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(201,162,74,.18), transparent 60%),
        radial-gradient(900px 500px at 90% 20%, rgba(231,215,161,.20), transparent 55%),
        linear-gradient(180deg, #fbfbfb 0%, #f3f4f6 100%);
    }

    .overlay{
      display:none;
      position:fixed;
      inset:0;
      background: rgba(17,24,39,.45);
      z-index:30;
    }

    .sidebar{
      position:fixed;
      top:0; left:0;
      height:100vh;
      width:16rem;
      overflow-y:auto;
      flex-shrink:0;
      transition:all .3s ease;
      z-index:40;

      background: var(--bb-glass-strong);
      backdrop-filter: blur(18px) saturate(140%);
      -webkit-backdrop-filter: blur(18px) saturate(140%);
      border-right: 1px solid var(--bb-border);
      box-shadow: 0 0 25px rgba(17,24,39,.06);
    }

    .sidebar::-webkit-scrollbar{ width:6px; }
    .sidebar::-webkit-scrollbar-thumb{
      background-color: rgba(17,24,39,.18);
      border-radius: 999px;
    }

    main{ margin-left:16rem; transition: margin-left .3s ease; }

    header{
      position:fixed;
      top:0; left:16rem; right:0;
      height:4.5rem;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 0 2rem;
      z-index:50;
      transition:left .3s ease;

      background: rgba(255,255,255,.70);
      backdrop-filter: blur(16px) saturate(140%);
      -webkit-backdrop-filter: blur(16px) saturate(140%);
      border-bottom: 1px solid var(--bb-border);
      box-shadow: 0 6px 20px rgba(17,24,39,.06);
    }

    .sidebar a{
      display:flex;
      align-items:center;
      padding:.75rem 1rem;
      border-radius: .95rem;
      margin:.25rem .5rem;
      text-decoration:none;
      position:relative;
      overflow:hidden;
      transition: all .25s ease;
      color: var(--bb-ink) !important;
    }

    .sidebar a i{
      width:1.5rem;
      text-align:center;
      margin-right:.75rem;
      color: rgba(201,162,74,.95) !important;
      transition: transform .25s ease, opacity .25s ease;
      opacity:.95;
    }

    .sidebar a::before{
      content:"";
      position:absolute;
      top:0; left:-120%;
      width:120%;
      height:100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.65), transparent);
      transition: left .6s ease;
    }
    .sidebar a:hover::before{ left:120%; }

    .sidebar a:hover{
      transform: translateX(4px);
      box-shadow: 0 10px 25px rgba(17,24,39,.08);
      background: rgba(255,255,255,.55);
    }
    .sidebar a:hover i{ transform: scale(1.08); }

    .sidebar a.bg-gray-200,
    .sidebar a.bg-orange-100,
    .sidebar a.bg-yellow-100,
    .sidebar a.bg-blue-100,
    .sidebar a.bg-purple-100,
    .sidebar a.bg-red-100,
    .sidebar a.bg-green-100,
    .sidebar a.bg-pink-100,
    .sidebar a.bg-teal-100,
    .sidebar a.bg-green-50{
      background: linear-gradient(135deg, rgba(201,162,74,.22), rgba(255,255,255,.60)) !important;
      color: var(--bb-ink) !important;
      font-weight: 700;
      border: 1px solid rgba(201,162,74,.28);
      box-shadow: 0 12px 28px rgba(201,162,74,.18);
    }
    .sidebar a.bg-gray-200::after,
    .sidebar a.bg-orange-100::after,
    .sidebar a.bg-yellow-100::after,
    .sidebar a.bg-blue-100::after,
    .sidebar a.bg-purple-100::after,
    .sidebar a.bg-red-100::after,
    .sidebar a.bg-green-100::after,
    .sidebar a.bg-pink-100::after,
    .sidebar a.bg-teal-100::after,
    .sidebar a.bg-green-50::after{
      content:"";
      position:absolute;
      right:0;
      top:50%;
      transform: translateY(-50%);
      width:4px;
      height:60%;
      border-radius:999px;
      background: var(--bb-gold);
    }

    .sidebar .text-gray-300,
    .sidebar .text-gray-400,
    .sidebar .text-gray-500,
    .sidebar .text-gray-600,
    .sidebar .text-gray-700{
      color: var(--bb-muted) !important;
    }

    .sidebar .bb-logo-wrap{
      border-bottom: 1px solid rgba(201,162,74,.18);
      background: rgba(255,255,255,.35);
    }

    .sidebar .rounded-full.bg-orange-500{
      background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2)) !important;
      color: #111827 !important;
      box-shadow: 0 10px 22px rgba(201,162,74,.22);
    }

    .bb-icon-btn{
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display:flex;
      align-items:center;
      justify-content:center;
      background: rgba(255,255,255,.65);
      border: 1px solid rgba(201,162,74,.22);
      box-shadow: 0 10px 22px rgba(17,24,39,.07);
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .bb-icon-btn:hover{
      transform: translateY(-1px);
      background: rgba(255,255,255,.80);
      box-shadow: 0 16px 30px rgba(17,24,39,.09);
    }
    .bb-icon-btn i{ color: rgba(17,24,39,.85); }

    .bb-notif-btn{
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95));
      border: 1px solid rgba(201,162,74,.35);
    }
    .bb-notif-btn i{ color:#111827; }

    #notifications-panel{
      background: rgba(255,255,255,.78);
      backdrop-filter: blur(16px) saturate(140%);
      -webkit-backdrop-filter: blur(16px) saturate(140%);
      border: 1px solid rgba(201,162,74,.20);
      box-shadow: 0 18px 45px rgba(17,24,39,.12);
    }

    .bg-white{
      background: rgba(255,255,255,.72) !important;
      backdrop-filter: blur(14px) saturate(140%);
      -webkit-backdrop-filter: blur(14px) saturate(140%);
      border: 1px solid rgba(255,255,255,.65) !important;
      box-shadow: 0 10px 26px rgba(17,24,39,.06);
      border-radius: 1rem;
    }

    @media (max-width:1024px){
      .sidebar{
        transform: translateX(-100%);
        z-index:50;
        box-shadow: 0 0 35px rgba(17,24,39,.12);
        padding-bottom: 5rem;
      }
      .sidebar.open{ transform: translateX(0); }
      .overlay.open{ display:block; }
      main{ margin-left:0 !important; padding:1rem; }
      .ml-64{ margin-left:0 !important; }
      header{ left:0 !important; width:100% !important; padding:0 1rem; }

      .sidebar .p-4.border-t{
        position: sticky;
        bottom:0;
        z-index:60;
        background: rgba(255,255,255,.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid var(--bb-border);
      }
    }

    #theme-icon.fa-sun{ color: var(--bb-gold) !important; }
  </style>
</head>

<body class="min-h-screen flex transition-colors duration-300">
  <div id="overlay" class="overlay"></div>

  <!-- ✅ Sidebar -->
  <nav id="sidebar" class="sidebar w-64 flex-shrink-0 flex flex-col justify-between fixed left-0 top-0 h-screen overflow-y-auto z-40">
    <div>
      <div class="bb-logo-wrap p-4 flex items-center justify-center">
        <img src="{{ asset('iconos/logo.png') }}" alt="Logo" class="h-12">
      </div>

      <div class="p-4">
        @if(Auth::user()->role_id == 3)
          @include('components.sidebar-admin')
        @else
          @include('components.sidebar-cliente')
        @endif
      </div>
    </div>

    <div class="p-4 border-t border-gray-200">
      <div class="flex items-center space-x-3 mb-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-500 text-white font-bold text-lg">
          {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div>
          <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
          <p class="text-sm text-gray-500 capitalize">
            {{ Auth::user()->role_id == 3 ? 'Administrador' : 'Cliente' }}
          </p>
        </div>
      </div>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="w-full flex items-center justify-center p-3 text-white bg-red-500 rounded-xl hover:bg-red-600 transition duration-200 shadow-md">
          <i class="fas fa-sign-out-alt mr-2"></i>
          <span>Salir</span>
        </button>
      </form>
    </div>
  </nav>

  <!-- ✅ Header -->
  <header class="flex items-center justify-between px-6 py-4 fixed top-0 left-64 right-0 z-30 transition-all duration-300">
    <div class="flex items-center space-x-3">
      <button id="sidebar-toggle" class="md:hidden bb-icon-btn" aria-label="Abrir menú">
        <i class="fas fa-bars text-lg"></i>
      </button>
      <h2 class="text-lg font-semibold">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center space-x-3">
      <button id="theme-toggle" class="bb-icon-btn" aria-label="Cambiar tema">
        <i class="fas fa-moon" id="theme-icon"></i>
      </button>

      <div class="relative">
        <button id="notifications-toggle" class="bb-icon-btn bb-notif-btn relative" aria-label="Notificaciones">
          <i class="fas fa-bell"></i>
          <span id="notification-count"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center hidden">0</span>
        </button>

        <div id="notifications-panel"
             class="absolute right-0 mt-3 w-80 rounded-2xl z-50 hidden max-h-96 overflow-y-auto">
          <div class="p-4 border-b border-gray-200">
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

  <!-- Main -->
  <main class="flex-1 p-4 ml-64 mt-20 overflow-y-auto">
    <div class="max-w-6xl mx-auto">
      @if(session('ok'))
        <div class="mb-4 p-3 rounded-xl bg-green-100 text-green-800">{{ session('ok') }}</div>
      @endif

      @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </main>

  <!-- ✅ JS build (si existe) -->
  @if(isset($manifest['resources/js/app.js']))
    <script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
  @endif

  @stack('scripts')
  @yield('scripts')

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const notificationsToggle = document.getElementById('notifications-toggle');
    const notificationsPanel = document.getElementById('notifications-panel');
    const body = document.body;

    sidebarToggle?.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
    });
    overlay?.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
    });

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

    const savedTheme = localStorage.getItem('darkMode');
    if (savedTheme === 'true') {
      body.classList.add('dark-mode');
      themeIcon.classList.remove('fa-moon');
      themeIcon.classList.add('fa-sun');
    }
    themeToggle?.addEventListener('click', toggleTheme);

    notificationsToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      notificationsPanel.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!notificationsToggle?.contains(e.target) && !notificationsPanel?.contains(e.target)) {
        notificationsPanel?.classList.add('hidden');
      }
    });

    function loadNotifications() {
      const notificationsList = document.getElementById('notifications-list');
      if (notificationsList) {
        notificationsList.innerHTML = `
          <div class="p-3 border-b border-gray-200">
            <p class="text-sm text-gray-700">No hay notificaciones nuevas</p>
            <p class="text-xs text-gray-500">Todo está al día</p>
          </div>
        `;
      }
    }
    notificationsToggle?.addEventListener('click', loadNotifications);

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('[data-submenu]').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
          e.preventDefault();
          const id = toggle.getAttribute('data-submenu');
          const menu = document.getElementById(id);
          if (!menu) return;

          menu.classList.toggle('hidden');
          const expanded = !menu.classList.contains('hidden');
          toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');

          const chevron = toggle.querySelector('.nav-chevron');
          if (chevron) chevron.classList.toggle('open', expanded);
        });
      });
    });
  </script>
</body>
</html>
