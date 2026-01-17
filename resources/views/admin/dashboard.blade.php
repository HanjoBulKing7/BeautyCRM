@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

  {{-- ✅ Tus estilos (idénticos) --}}
  <style>
    :root{
      --bb-gold: rgba(201,162,74,.95);
      --bb-gold-soft: rgba(201,162,74,.14);
      --bb-gold-border: rgba(201,162,74,.22);
      --bb-ink: rgba(17,24,39,.92);
      --bb-muted: rgba(107,114,128,.92);
      --bb-border: rgba(17,24,39,.08);
      --bb-glass: rgba(255,255,255,.72);
      --bb-glass-2: rgba(255,255,255,.55);
    }

    .bb-glass-card{
      background: var(--bb-glass);
      backdrop-filter: blur(14px) saturate(140%);
      -webkit-backdrop-filter: blur(14px) saturate(140%);
      border: 1px solid rgba(255,255,255,.65);
      box-shadow: 0 10px 26px rgba(17,24,39,.06);
      border-radius: 1rem;
    }

    .bb-icon-pill{
      width: 40px; height: 40px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255,255,255,.55);
      border: 1px solid rgba(201,162,74,.18);
      box-shadow: 0 10px 22px rgba(17,24,39,.06);
    }
    .bb-gold{ color: var(--bb-gold) !important; }

    .bb-btn-gold{
      display:inline-flex;
      align-items:center;
      gap:.5rem;
      padding:.6rem 1rem;
      border-radius: .95rem;
      font-weight: 800;
      color: #111827;
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95));
      border: 1px solid rgba(201,162,74,.35);
      box-shadow: 0 12px 28px rgba(201,162,74,.18);
      transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
      white-space: nowrap;
    }
    .bb-btn-gold:hover{
      transform: translateY(-1px);
      box-shadow: 0 18px 40px rgba(17,24,39,.10);
      filter: brightness(1.02);
    }

    .bb-btn-ghost{
      display:inline-flex;
      align-items:center;
      gap:.5rem;
      padding:.6rem 1rem;
      border-radius: .95rem;
      font-weight: 700;
      color: rgba(17,24,39,.88);
      background: rgba(255,255,255,.60);
      border: 1px solid rgba(255,255,255,.65);
      box-shadow: 0 10px 22px rgba(17,24,39,.06);
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
      white-space: nowrap;
    }
    .bb-btn-ghost:hover{
      transform: translateY(-1px);
      background: rgba(255,255,255,.78);
      box-shadow: 0 16px 30px rgba(17,24,39,.08);
    }

    .bb-input{
      width: 100%;
      border-radius: .95rem;
      border: 1px solid rgba(17,24,39,.10);
      background: rgba(255,255,255,.70);
      padding: .6rem .8rem;
      outline: none;
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    .bb-input:focus{
      border-color: rgba(201,162,74,.28);
      box-shadow: 0 0 0 3px rgba(201,162,74,.18);
    }

    .bb-thead{
      background: rgba(255,255,255,.35);
      border-bottom: 1px solid var(--bb-border);
    }

    .bb-row{
      border-bottom: 1px solid rgba(17,24,39,.06);
      transition: background .2s ease;
    }
    .bb-row:hover{ background: rgba(255,255,255,.45); }

    .bb-pill{
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      padding: .35rem .65rem;
      border-radius: .8rem;
      background: rgba(255,255,255,.55);
      border: 1px solid rgba(255,255,255,.60);
      font-weight: 700;
    }
    .bb-pill-gold{
      background: var(--bb-gold-soft);
      border-color: var(--bb-gold-border);
      color: rgba(17,24,39,.90);
    }

    .bb-action{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:.4rem;
      padding: .55rem .75rem;
      border-radius: .9rem;
      background: rgba(255,255,255,.50);
      border: 1px solid rgba(255,255,255,.55);
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
      white-space: nowrap;
    }
    .bb-action:hover{
      transform: translateY(-1px);
      background: rgba(255,255,255,.70);
      box-shadow: 0 12px 24px rgba(17,24,39,.08);
    }
    .bb-action-gold{ color: var(--bb-gold) !important; border-color: rgba(201,162,74,.18) !important; }
    .bb-action-ink{ color: rgba(17,24,39,.85) !important; }

    /* ✅ Responsive: tabla solo desktop, cards solo móvil */
    @media (max-width: 768px){
      .bb-hide-md { display:none !important; }
      .bb-show-md { display:block !important; }
    }
    @media (min-width: 769px){
      .bb-show-md { display:none !important; }
    }

    /* Dark mode (si lo usas) */
    .dark-mode .bb-glass-card{
      background: rgba(17,24,39,.55);
      border-color: rgba(255,255,255,.10);
    }
    .dark-mode .bb-thead{
      background: rgba(17,24,39,.45);
      border-bottom-color: rgba(255,255,255,.10);
    }
    .dark-mode .bb-row{ border-bottom-color: rgba(255,255,255,.08); }
    .dark-mode .bb-row:hover{ background: rgba(255,255,255,.06); }
    .dark-mode .bb-input{
      background: rgba(17,24,39,.35);
      border-color: rgba(255,255,255,.10);
      color: rgba(249,250,251,.92);
    }
    .dark-mode .bb-input:focus{ box-shadow: 0 0 0 3px rgba(201,162,74,.16); }
    .dark-mode .text-gray-800{ color: rgba(249,250,251,.95) !important; }
    .dark-mode .text-gray-700{ color: rgba(229,231,235,.92) !important; }
    .dark-mode .text-gray-600{ color: rgba(209,213,219,.86) !important; }
    .dark-mode .text-gray-500{ color: rgba(156,163,175,.92) !important; }
    .dark-mode .text-gray-900{ color: rgba(249,250,251,.98) !important; }
  </style>

  {{-- ✅ Header: título izquierda, botones derecha --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
        <span class="bb-icon-pill">
          <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V6a2 2 0 012-2h4a2 2 0 012 2v1m3 0a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h14z"/>
          </svg>
        </span>
        Dashboard de Citas
      </h1>
    </div>

    <div class="flex gap-2 sm:justify-end">
      <a href="#" class="bb-btn-gold">➕ Registrar cita</a>
      <a href="#" class="bb-btn-ghost">💰 Ver ventas</a>
    </div>
  </div>

  {{-- ✅ Filtro minimalista (NO ocupa mucho) --}}
  <div class="bb-glass-card px-4 py-3 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex items-center gap-2">
        <button type="button" class="bb-action bb-action-ink" title="Anterior">◀</button>

        <input type="date" class="bb-input" style="max-width: 190px;" value="{{ now()->format('Y-m-d') }}">

        <button type="button" class="bb-action bb-action-ink" title="Siguiente">▶</button>

        <button type="button" class="bb-action bb-action-gold" title="Ir a hoy">Hoy</button>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">
          Mostrando:
          <span class="font-semibold text-gray-800">{{ now()->format('d/m/Y') }}</span>
        </span>
      </div>
    </div>
  </div>

  {{-- ✅ Resumen (hardcode por ahora) --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">💰</span></div>
        <div>
          <p class="text-sm text-gray-500">Generado</p>
          <p class="text-xl font-extrabold bb-gold">$1,250.00</p>
        </div>
      </div>
    </div>
    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">📅</span></div>
        <div>
          <p class="text-sm text-gray-500">Citas del día</p>
          <p class="text-xl font-extrabold text-gray-900">6</p>
        </div>
      </div>
    </div>
    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">✅</span></div>
        <div>
          <p class="text-sm text-gray-500">Confirmadas</p>
          <p class="text-xl font-extrabold text-gray-900">4</p>
        </div>
      </div>
    </div>
    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">⏳</span></div>
        <div>
          <p class="text-sm text-gray-500">Pendientes</p>
          <p class="text-xl font-extrabold text-gray-900">2</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ✅ Tabla Desktop (ejemplo) --}}
  <div class="bb-glass-card overflow-hidden bb-hide-md">
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead class="bb-thead">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hora</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Servicio</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Empleado</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Venta</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr class="bb-row">
            <td class="px-4 py-3 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">10:00</div></td>
            <td class="px-4 py-3 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">María López</div>
              <div class="text-xs text-gray-500">maria@email.com</div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-900">Maquillaje Profesional</div>
              <div class="text-xs text-gray-500">$450.00</div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap"><div class="text-sm text-gray-900">Karla</div></td>
            <td class="px-4 py-3 whitespace-nowrap"><span class="bb-pill bb-pill-gold">CONFIRMADA</span></td>
            <td class="px-4 py-3 whitespace-nowrap"><span class="bb-pill bb-pill-gold">$450.00</span></td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <a class="bb-action bb-action-ink" href="#">👁 Ver</a>
              </div>
            </td>
          </tr>

          <tr class="bb-row">
            <td class="px-4 py-3 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">12:30</div></td>
            <td class="px-4 py-3 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">Ana Pérez</div>
              <div class="text-xs text-gray-500">ana@email.com</div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-900">Coloración</div>
              <div class="text-xs text-gray-500">$800.00</div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap"><div class="text-sm text-gray-900">Diana</div></td>
            <td class="px-4 py-3 whitespace-nowrap"><span class="bb-pill">PENDIENTE</span></td>
            <td class="px-4 py-3 whitespace-nowrap"><span class="text-xs text-gray-500">Sin venta</span></td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button class="bb-action bb-action-gold" type="button">✅ Confirmar</button>
                <a class="bb-action bb-action-ink" href="#">💳 Venta</a>
                <a class="bb-action bb-action-ink" href="#">👁 Ver</a>
              </div>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

  {{-- ✅ Cards Mobile (ejemplo) --}}
  <div class="bb-show-md space-y-3">
    <div class="bb-glass-card p-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs text-gray-500">Hora</div>
          <div class="text-lg font-extrabold text-gray-900">10:00</div>
        </div>
        <span class="bb-pill bb-pill-gold">CONFIRMADA</span>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-2">
        <div>
          <div class="text-xs text-gray-500">Cliente</div>
          <div class="text-sm font-semibold text-gray-900">María López</div>
          <div class="text-xs text-gray-500">maria@email.com</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Servicio</div>
          <div class="text-sm text-gray-900">Maquillaje Profesional</div>
          <div class="text-xs text-gray-500">$450.00</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Empleado</div>
          <div class="text-sm text-gray-900">Karla</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Venta</div>
          <div class="text-sm font-semibold bb-gold">$450.00</div>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a class="bb-action bb-action-ink" href="#">👁 Ver</a>
      </div>
    </div>

    <div class="bb-glass-card p-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs text-gray-500">Hora</div>
          <div class="text-lg font-extrabold text-gray-900">12:30</div>
        </div>
        <span class="bb-pill">PENDIENTE</span>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-2">
        <div>
          <div class="text-xs text-gray-500">Cliente</div>
          <div class="text-sm font-semibold text-gray-900">Ana Pérez</div>
          <div class="text-xs text-gray-500">ana@email.com</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Servicio</div>
          <div class="text-sm text-gray-900">Coloración</div>
          <div class="text-xs text-gray-500">$800.00</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Empleado</div>
          <div class="text-sm text-gray-900">Diana</div>
        </div>

        <div>
          <div class="text-xs text-gray-500">Venta</div>
          <div class="text-sm text-gray-500">Sin venta</div>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <button class="bb-action bb-action-gold" type="button">✅ Confirmar</button>
        <a class="bb-action bb-action-ink" href="#">💳 Venta</a>
        <a class="bb-action bb-action-ink" href="#">👁 Ver</a>
      </div>
    </div>
  </div>

</div>
@endsection
