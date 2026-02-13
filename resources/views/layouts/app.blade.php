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

  /* ============ Overlay ============ */
  .overlay{
    display:none;
    position:fixed;
    inset:0;
    background: rgba(17,24,39,.45);
    z-index:30;
  }

  /* ============ Sidebar ============ */
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

  /* Links */
  .sidebar a{
    display:flex;
    align-items:center;
    padding:.75rem 1rem;
    border-radius:.95rem;
    margin:.25rem .5rem;
    text-decoration:none;
    position:relative;
    overflow:hidden;
    transition: all .25s ease;
    color: var(--bb-ink) !important;

    background: rgba(255,255,255,.70);
    backdrop-filter: blur(16px) saturate(140%);
    -webkit-backdrop-filter: blur(16px) saturate(140%);

    /* ✅ quitar “bordes” reales y visuales */
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;   /* <-- ESTA es la clave */
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

  /* Active state dorado (cuando el backend pone bg-*-100) */
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
    border: 0 !important;
    outline: 0 !important;

    /* 👇 si aquí también lo ves como “borde”, quítalo */
    box-shadow: 0 12px 28px rgba(201,162,74,.18) !important; 
    /* o si lo quieres 100% plano:
    box-shadow: none !important;
    */
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

  /* ============ Layout containers ============ */
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

  /* Icon buttons */
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

  /* Cards "glass" (si tu UI usa bg-white) */
  .bg-white{
    background: rgba(255,255,255,.72) !important;
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    /*border: 1px solid rgba(255,255,255,.65) !important;*/
    box-shadow: 0 10px 26px rgba(17,24,39,.06);
    border-radius: 1rem;
  }
  
  /* ===============================
    Dark Mode (cuando body tiene .dark-mode)
  ================================ */
  body.dark-mode{
    color: #E5E7EB;
    background:
      radial-gradient(1200px 600px at 15% 10%, rgba(201,162,74,.10), transparent 60%),
      radial-gradient(900px 500px at 90% 20%, rgba(231,215,161,.10), transparent 55%),
      linear-gradient(180deg, #0B1220 0%, #0A0F1A 100%);
  }

  body.dark-mode header{
    background: rgba(17,24,39,.62);
    border-bottom: 1px solid rgba(255,255,255,.08);
  }

  body.dark-mode .sidebar{
    background: rgba(17,24,39,.58);
    border-right: 1px solid rgba(255,255,255,.08);
  }

  body.dark-mode .sidebar a{
    background: rgba(17,24,39,.35);
    color: #E5E7EB !important;
  }

  body.dark-mode .sidebar a i{
    color: rgba(231,215,161,.95) !important;
  }

  body.dark-mode .bg-white{
    background: rgba(17,24,39,.42) !important;
    border: 1px solid rgba(255,255,255,.08) !important;
  }


  /* Responsive */
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
    /* ===============================
        Dark Mode (cuando body tiene .dark-mode)
      ================================ */
      body.dark-mode{
        color: #E5E7EB;
        background:
          radial-gradient(1200px 600px at 15% 10%, rgba(201,162,74,.10), transparent 60%),
          radial-gradient(900px 500px at 90% 20%, rgba(231,215,161,.10), transparent 55%),
          linear-gradient(180deg, #0B1220 0%, #0A0F1A 100%);
      }

      body.dark-mode header{
        background: rgba(17,24,39,.62);
        border-bottom: 1px solid rgba(255,255,255,.08);
      }

      body.dark-mode .sidebar{
        background: rgba(17,24,39,.58);
        border-right: 1px solid rgba(255,255,255,.08);
      }

      body.dark-mode .sidebar a{
        background: rgba(17,24,39,.35);
        color: #E5E7EB !important;
      }

      body.dark-mode .sidebar a i{
        color: rgba(231,215,161,.95) !important;
      }

      body.dark-mode .bg-white{
        background: rgba(17,24,39,.42) !important;
        border: 1px solid rgba(255,255,255,.08) !important;
      }

  }

  #theme-icon.fa-sun{ color: var(--bb-gold) !important; }

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
    padding: 7px;
  }

  .bb-modal.open{
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .bb-modal-card{
    width: min(1400px, 120vw);
    height: min(88vh, 980px);
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
  <script src="{{ asset('js/empleados-servicios.js') }}"></script>


  @stack('scripts')
  @yield('scripts')


  <script>
window.bbInitServicioForm = function(root = document) {
  // ===== Preview imagen =====
  const input = root.querySelector("#imagenInput");
  const preview = root.querySelector("#imagenPreview");

  if (preview && preview.getAttribute("src")) {
    preview.classList.remove("hidden");
  }

  if (input && !input.dataset.bbBound) {
    input.dataset.bbBound = "1";
    input.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      if (!file.type.startsWith("image/")) {
        alert("Selecciona un archivo de imagen válido.");
        input.value = "";
        return;
      }

      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.classList.remove("hidden");
    });
  }

  // ===== Horarios =====
  const horariosWrapper = root.querySelector('#bb-horarios-wrapper');
  if (!horariosWrapper) return;

  if (horariosWrapper.dataset.bbBound) return;
  horariosWrapper.dataset.bbBound = "1";

  const nextIndexForDay = (day) => {
    const dayWrap = horariosWrapper.querySelector(`[data-dia-wrap="${day}"]`);
    return dayWrap ? dayWrap.querySelectorAll('[data-horario-row]').length : 0;
  };

  horariosWrapper.addEventListener('click', (e) => {
    const addBtn = e.target.closest('[data-add-horario]');
    if (addBtn) {
      const day = addBtn.getAttribute('data-dia');
      const dayWrap = horariosWrapper.querySelector(`[data-dia-wrap="${day}"]`);
      if (!dayWrap) return;

      const i = nextIndexForDay(day);

      const row = document.createElement('div');
      row.className = 'flex flex-col sm:flex-row gap-2 items-start sm:items-center bg-gray-50 rounded-lg p-2';
      row.setAttribute('data-horario-row', '');
      row.innerHTML = `
        <div class="flex gap-2 items-center w-full sm:w-auto">
          <label class="text-xs text-gray-600 w-16">Inicio</label>
          <input type="time" name="horarios[${day}][${i}][hora_inicio]"
                 class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
        </div>

        <div class="flex gap-2 items-center w-full sm:w-auto">
          <label class="text-xs text-gray-600 w-16">Fin</label>
          <input type="time" name="horarios[${day}][${i}][hora_fin]"
                 class="w-full sm:w-40 border border-gray-300 rounded-lg px-3 py-2">
        </div>

        <button type="button"
                class="ml-auto text-xs px-3 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                data-remove-horario>Quitar</button>
      `;
      dayWrap.appendChild(row);
      return;
    }

    const removeBtn = e.target.closest('[data-remove-horario]');
    if (removeBtn) removeBtn.closest('[data-horario-row]')?.remove();
  });
};
</script>

<script>
(() => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const bbHost = document.getElementById('bb-module-host'); // si existe (dashboard)
  const bbModal = document.getElementById('bb-modal');
  const bbModalBackdrop = document.getElementById('bb-modal-backdrop');
  const bbModalTitle = document.getElementById('bb-modal-title');
  const bbModalBody = document.getElementById('bb-modal-body');
  const bbModalClose = document.getElementById('bb-modal-close');

  function bbOpenModal(){
    if(!bbModal) return;
    bbModal.classList.add('open');
    bbModalBackdrop?.classList.add('open');
    bbModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function bbCloseModal(){
    if(!bbModal) return;
    bbModal.classList.remove('open');
    bbModalBackdrop?.classList.remove('open');
    bbModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  bbModalClose?.addEventListener('click', bbCloseModal);
  bbModalBackdrop?.addEventListener('click', bbCloseModal);
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape') bbCloseModal(); });

  function bbSetLoading(el){
    if(!el) return;
    el.innerHTML = `<div class="bg-white p-4 rounded-xl">
      <div class="text-sm text-gray-500">Cargando…</div>
    </div>`;
  }

  function bbRenderHtmlInto(targetEl, html){
    const doc = new DOMParser().parseFromString(html, 'text/html');

    // intenta tomar el <main> del layout
    const main = doc.querySelector('main');
    targetEl.innerHTML = main ? main.innerHTML : html;
  }

  // =========================
  // ABRIR LINK EN MODAL
  // =========================
  async function bbLoadModalOnly(href, title = 'Módulo'){
    if(!bbModalBody){ window.location.href = href; return; }

    if(!bbModalBody){
      // si no existe modal en esta página, navega normal
      window.location.href = href;
      return;
    }

    const url = new URL(href, window.location.href);
    url.searchParams.set('modal', '1');

    if(bbModalTitle) bbModalTitle.textContent = title;
    bbSetLoading(bbModalBody);
    bbOpenModal();

    const res = await fetch(url.href, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
      },
      credentials: 'same-origin',
    });

    const html = await res.text();

    // Si el server truena (500), muestra mensaje y loggea en consola
    if(res.status >= 500){
      console.error('Error 500 al cargar modal:', url.href, html);
      bbModalBody.innerHTML = `
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
          Ocurrió un error al cargar este formulario. Revisa la consola y laravel.log.
        </div>`;
      return;
    }

    bbRenderHtmlInto(bbModalBody, html);
    window.bbInitServicioForm?.(bbModalBody);
  }

  // =========================
  // INTERCEPTAR CLICKS (CAPTURE)
  // =========================
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('a[data-bb-open="modal"], a[data-bb-modal], a[data-bb-modal="1"], [data-bb-modal="1"]');
    if(!trigger) return;

    if(trigger.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

    const href =
      trigger.getAttribute('data-bb-url') ||
      trigger.getAttribute('data-url') ||
      trigger.getAttribute('href');

    if(!href || href.startsWith('#') || href.startsWith('javascript:')) return;

    const title =
      trigger.getAttribute('data-bb-title') ||
      trigger.getAttribute('data-title') ||
      (trigger.textContent || '').trim() ||
      'Módulo';

    e.preventDefault();
    e.stopPropagation();

    bbLoadModalOnly(href, title);
  }, true);

  // =========================
  // SUBMITS DENTRO DEL MODAL
  // =========================
  async function bbHandleSubmit(container, e){
    const form = e.target;
    if(!(form instanceof HTMLFormElement)) return;
    if(!container || !container.contains(form)) return;

    e.preventDefault();

    const action = form.getAttribute('action') || window.location.href;
    const fd = new FormData(form);
    if(!fd.has('modal')) fd.append('modal', '1');

    const res = await fetch(action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      },
      credentials: 'same-origin',
      body: fd,
    });

    const finalUrl = new URL(res.url, window.location.href);
    const html = await res.text();

    // ✅ Si el submit venía del MODAL:
    if(container === bbModalBody){
      // Éxito: terminó en el index (misma pantalla de detrás)
      if(finalUrl.pathname === window.location.pathname){
        bbCloseModal();
        window.location.reload();
        return;
      }

      // Error: volvió a create/edit con errores → renderiza form con errores en el modal
      bbRenderHtmlInto(container, html);
      window.bbInitServicioForm?.(container);

      window.bbInitServicioForm?.(bbModalBody);
      return;
    }

    // host/dashboard (si lo usas en otras pantallas)
    bbRenderHtmlInto(container, html);
  }

  document.addEventListener('submit', (e) => { if(bbModalBody) bbHandleSubmit(bbModalBody, e); }, true);
  if(bbHost){
    document.addEventListener('submit', (e) => bbHandleSubmit(bbHost, e), true);
  }
})();
</script>



<script>
(() => {
  const body = document.body;

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const sidebarToggle = document.getElementById('sidebar-toggle');

  const themeToggle = document.getElementById('theme-toggle');
  const themeIcon = document.getElementById('theme-icon');

  // =========================
  // Sidebar móvil (si existe)
  // =========================
  sidebarToggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    overlay?.classList.toggle('open');
  });

  overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
  });

  // =========================
  // Theme toggle (dark-mode)
  // =========================
  function applyTheme(isDark) {
    // ✅ Para tu CSS propio
    body.classList.toggle('dark-mode', isDark);

    // ✅ Para que funcionen las clases Tailwind "dark:*"
    document.documentElement.classList.toggle('dark', isDark);

    if (themeIcon) {
      themeIcon.classList.toggle('fa-moon', !isDark);
      themeIcon.classList.toggle('fa-sun', isDark);
    }

    localStorage.setItem('darkMode', isDark ? 'true' : 'false');
  }


  // cargar tema guardado
  const saved = localStorage.getItem('darkMode');
  applyTheme(saved === 'true');

  // click botón
  themeToggle?.addEventListener('click', () => {
    const isDark = !body.classList.contains('dark-mode');
    applyTheme(isDark);
  });
})();
</script>


</body>
</html>
```
