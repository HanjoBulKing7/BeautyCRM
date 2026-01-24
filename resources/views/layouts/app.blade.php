```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    @if(file_exists(public_path('css/components.css')))
      <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @endif

    @if(file_exists(public_path('css/ui.css')))
      <link rel="stylesheet" href="{{ asset('css/ui.css') }}">
    @endif
  @endif

  {{-- ✅ COMPONENTS.CSS --}}
  @if(isset($manifest['resources/css/components.css']))
    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/components.css']['file']) }}">
  @endif

  {{-- ✅ UI.CSS --}}
  @if(isset($manifest['resources/css/ui.css']))
    <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/ui.css']['file']) }}">
  @endif

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  @stack('styles')

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

    .bb-notif-btn{
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95));
      border: 1px solid rgba(201,162,74,.35);
    }

    #notifications-panel{
      background: rgba(255,255,255,.78);
      backdrop-filter: blur(16px) saturate(140%);
      -webkit-backdrop-filter: blur(16px) saturate(140%);
      border: 1px solid rgba(201,162,74,.20);
      box-shadow: 0 18px 45px rgba(17,24,39,.12);
    }

    @media (max-width:1024px){
      .sidebar{ transform: translateX(-100%); z-index:50; }
      .sidebar.open{ transform: translateX(0); }
      .overlay.open{ display:block; }
      main{ margin-left:0 !important; padding:1rem; }
      header{ left:0 !important; width:100% !important; padding:0 1rem; }
    }

    /* ====== BeautyCRM Modal (fallback) ====== */
    .bb-modal-backdrop{
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      display: none;
      z-index: 9998;
    }
    .bb-modal-backdrop.open{ display:block; }

    .bb-modal{
      position: fixed;
      inset: 0;
      display: none;
      z-index: 9999;
      padding: 16px;
    }
    .bb-modal.open{
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .bb-modal-card{
      width: min(1100px, 96vw);
      height: min(84vh, 900px);
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(18px) saturate(140%);
      -webkit-backdrop-filter: blur(18px) saturate(140%);
      border: 1px solid rgba(255,255,255,.65);
      border-radius: 18px;
      box-shadow: 0 20px 70px rgba(17,24,39,.22);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .bb-modal-body{ flex: 1; overflow: auto; }
  </style>
</head>

<body class="min-h-screen flex transition-colors duration-300">
  <div id="overlay" class="overlay"></div>

  <!-- ✅ Sidebar -->
  <nav id="sidebar" class="sidebar w-64 flex-shrink-0 flex flex-col justify-between fixed left-0 top-0 h-screen overflow-y-auto z-40">
    <div>
      <div class="p-4 flex items-center justify-center">
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
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-content-center hidden">0</span>
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

  <!-- ✅ Modal fallback (para páginas que NO sean dashboard) -->
  <div id="bb-modal-backdrop" class="bb-modal-backdrop"></div>
  <div id="bb-modal" class="bb-modal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="bb-modal-card">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
        <h3 id="bb-modal-title" class="text-lg font-semibold text-gray-800">—</h3>
        <button id="bb-modal-close" type="button" class="bb-icon-btn" aria-label="Cerrar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div id="bb-modal-body" class="bb-modal-body p-5">
        <div class="text-sm text-gray-500">Cargando…</div>
      </div>
    </div>
  </div>

  {{-- ✅ JS build (si existe) --}}
  @if(isset($manifest['resources/js/app.js']))
    <script src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
  @endif

  {{-- ✅ Chart.js SIEMPRE --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  {{-- ✅ FullCalendar GLOBAL (para que funcione en /admin/home con HTML inyectado) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/es.global.min.js"></script>

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

    // ======================================================
    // ✅ INITs globales de módulos (para HTML inyectado)
    // ======================================================
    window.BB = window.BB || {};

    // Citas calendar (busca #citas-calendar con data-events)
    window.BB.initCitasCalendar = function(root = document){
      const el = root.querySelector('#citas-calendar');
      if(!el || typeof FullCalendar === 'undefined') return;

      const raw = el.getAttribute('data-events') || '[]';
      let events = [];
      try{ events = JSON.parse(raw); }catch(e){ events = []; }

      if (el._bbCalendar) {
        try { el._bbCalendar.destroy(); } catch(e) {}
        el._bbCalendar = null;
      }

      const calendar = new FullCalendar.Calendar(el, {
        locale: 'es',
        firstDay: 1,
        initialView: 'dayGridMonth',
        height: 'auto',
        nowIndicator: true,
        selectable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'Agenda'
        },
        events,
      });

      el._bbCalendar = calendar;
      calendar.render();
      setTimeout(() => calendar.updateSize(), 50);
    };

    // Llama a todos los init que existan (crece a futuro)
    function bbRunInits(root){
      if (window.BB?.initCitasCalendar) window.BB.initCitasCalendar(root);
    }

    // ======================================================
    // BeautyCRM Hub Loader:
    // - Si existe #bb-module-host => carga dentro del dashboard y OCULTA #bb-dashboard-only
    // - Si NO existe => abre modal fallback
    // ======================================================

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const bbHost = document.getElementById('bb-module-host');              // solo dashboard
    const bbDashboardOnly = document.getElementById('bb-dashboard-only');  // solo dashboard

    const bbModal = document.getElementById('bb-modal');
    const bbModalBackdrop = document.getElementById('bb-modal-backdrop');
    const bbModalTitle = document.getElementById('bb-modal-title');
    const bbModalBody = document.getElementById('bb-modal-body');
    const bbModalClose = document.getElementById('bb-modal-close');
    const bbDashboardHeader = document.getElementById('bb-dashboard-header');

    function bbEnterModuleView(){
      if (bbDashboardOnly) bbDashboardOnly.classList.add('hidden');
      if (bbHost) bbHost.classList.remove('hidden');
      if (bbDashboardHeader) bbDashboardHeader.classList.add('hidden');
    }

    function bbExitModuleView(){
      if (bbHost){ bbHost.innerHTML = ''; bbHost.classList.add('hidden'); }
      if (bbDashboardOnly) bbDashboardOnly.classList.remove('hidden');
      if (bbDashboardHeader) bbDashboardHeader.classList.remove('hidden');
    }

    window.bbExitModuleView = bbExitModuleView;

    function bbOpenModal() {
      if (!bbModal) return;

      bbModal.classList.remove('open');
      bbModalBackdrop?.classList.remove('open');

      bbModal.classList.add('open');
      bbModalBackdrop?.classList.add('open');
      bbModal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function bbCloseModal() {
      if (!bbModal) return;
      bbModal.classList.remove('open');
      bbModalBackdrop?.classList.remove('open');
      bbModal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    bbModalClose?.addEventListener('click', bbCloseModal);
    bbModalBackdrop?.addEventListener('click', bbCloseModal);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') bbCloseModal(); });

    function bbSetLoading(targetEl) {
      if (!targetEl) return;
      targetEl.innerHTML = `
        <div class="bg-white p-4 rounded-xl">
          <div class="text-sm text-gray-500">Cargando…</div>
        </div>
      `;
    }

    function bbRenderHtmlInto(targetEl, html) {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const contentNode = doc.querySelector('main .max-w-6xl');
      targetEl.innerHTML = contentNode ? contentNode.innerHTML : html;
    }

    async function bbLoadModule(href, title = 'Módulo') {
      // cerrar modal si existiera
      bbCloseModal();

      const url = new URL(href, window.location.href);

      // ✅ el dashboard NUNCA se carga dentro del host (evita anidado)
      if (bbHost && url.pathname === '/admin/home') {
        bbExitModuleView();
        window.location.href = url.pathname + url.search;
        return;
      }

      // cerrar sidebar móvil
      sidebar?.classList.remove('open');
      overlay?.classList.remove('open');

      // forzar modo partial
      url.searchParams.set('modal', '1');

      // ✅ si estamos en dashboard => cargar en host
      if (bbHost) {
        bbEnterModuleView();
        bbHost.innerHTML = '';
        bbSetLoading(bbHost);

        const res = await fetch(url.href, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });

        if (res.redirected) return bbLoadModule(res.url, title);

        const html = await res.text();
        bbRenderHtmlInto(bbHost, html);

        // ✅ IMPORTANTE: inicializa JS del módulo (FullCalendar, etc.)
        bbRunInits(bbHost);

        bbHost.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
      }

      // ✅ fallback modal fuera del dashboard
      if (bbModalTitle) bbModalTitle.textContent = title;
      if (bbModalBody) bbSetLoading(bbModalBody);
      bbOpenModal();

      const res = await fetch(url.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });

      if (res.redirected) return bbLoadModule(res.url, title);

      const html = await res.text();
      if (bbModalBody) {
        bbRenderHtmlInto(bbModalBody, html);

        // ✅ IMPORTANTE: inicializa JS del módulo
        bbRunInits(bbModalBody);
      }
    }

    const isDashboardPage = !!bbHost && window.location.pathname === '/admin/home';

    document.addEventListener('click', (e) => {
      const a = e.target.closest('a,[data-bb-modal="1"]');
      if (!a) return;

      if (a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

      const hasFlag = a.hasAttribute('data-bb-modal') || a.getAttribute('data-bb-modal') === '1';
      if (!hasFlag) return;

      // ✅ SOLO interceptar en /admin/home
      if (!isDashboardPage) return;

      const href = a.getAttribute('data-bb-url') || a.getAttribute('data-url') || a.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

      e.preventDefault();

      const title = a.getAttribute('data-bb-title') || a.getAttribute('data-title') || (a.textContent || '').trim() || 'Módulo';
      bbLoadModule(href, title);
    });

    // 2) Links internos dentro del módulo (host o modal)
    function bbAttachInternalNav(containerGetter) {
      document.addEventListener('click', (e) => {
        const container = containerGetter();
        if (!container || !container.contains(e.target)) return;

        const a = e.target.closest('a');
        if (!a) return;

        if (a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const href =
          a.getAttribute('data-bb-url') ||
          a.getAttribute('data-url') ||
          a.getAttribute('href');

        if (!href) return;

        const url = new URL(href, window.location.href);
        const isSameOrigin = url.origin === window.location.origin;
        const isAdminPath  = isSameOrigin && url.pathname.startsWith('/admin');

        if (!isAdminPath) return;

        // ✅ Si es dashboard, no lo metas al host (solo vuelve)
        if (bbHost && url.pathname === '/admin/home') {
          e.preventDefault();
          bbExitModuleView();
          history.pushState({}, '', url.pathname + url.search);
          return;
        }

        e.preventDefault();
        bbLoadModule(url.href, bbModalTitle?.textContent || 'Módulo');
      }, true);
    }

    bbAttachInternalNav(() => bbHost);
    bbAttachInternalNav(() => bbModalBody);

    // 3) Submits dentro del módulo (host o modal)
    async function bbHandleSubmit(container, e) {
      const form = e.target;
      if (!(form instanceof HTMLFormElement)) return;
      if (!container || !container.contains(form)) return;

      e.preventDefault();

      const action = form.getAttribute('action') || window.location.href;
      const method = (form.getAttribute('method') || 'POST').toUpperCase();
      const fd     = new FormData(form);

      // forzar modo partial
      if (!fd.has('modal')) fd.append('modal', '1');

      if (method === 'GET') {
        const u = new URL(action, window.location.href);
        for (const [k, v] of fd.entries()) u.searchParams.set(k, String(v));
        return bbLoadModule(u.href, bbModalTitle?.textContent || 'Módulo');
      }

      const res = await fetch(action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        },
        credentials: 'same-origin',
        body: fd,
      });

      if (res.redirected) return bbLoadModule(res.url, bbModalTitle?.textContent || 'Módulo');

      const html = await res.text();
      bbRenderHtmlInto(container, html);

      // ✅ IMPORTANTE: re-inicializa JS del módulo (por ejemplo, si regresas a citas index)
      bbRunInits(container);
    }

    document.addEventListener('submit', (e) => { if (bbHost) bbHandleSubmit(bbHost, e); }, true);
    document.addEventListener('submit', (e) => { if (bbModalBody) bbHandleSubmit(bbModalBody, e); }, true);
  </script>
</body>
</html>
```
