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
    .bb-btn-gold:hover{ transform: translateY(-1px); box-shadow: 0 18px 40px rgba(17,24,39,.10); }

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
    .bb-btn-ghost:hover{ transform: translateY(-1px); background: rgba(255,255,255,.78); }

    .bb-input{
      width: 100%;
      border-radius: .95rem;
      border: 1px solid rgba(17,24,39,.10);
      background: rgba(255,255,255,.70);
      padding: .6rem .8rem;
      outline: none;
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    .bb-input:focus{ border-color: rgba(201,162,74,.28); box-shadow: 0 0 0 3px rgba(201,162,74,.18); }

    .bb-thead{ background: rgba(255,255,255,.35); border-bottom: 1px solid var(--bb-border); }
    .bb-row{ border-bottom: 1px solid rgba(17,24,39,.06); }
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
    .bb-pill-gold{ background: var(--bb-gold-soft); border-color: var(--bb-gold-border); color: rgba(17,24,39,.90); }
    .bb-pill-green{ background: rgba(34,197,94,.12); border-color: rgba(34,197,94,.22); color: rgba(17,24,39,.92); }
    .bb-pill-red{ background: rgba(239,68,68,.12); border-color: rgba(239,68,68,.22); color: rgba(17,24,39,.92); }

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
    .bb-action:hover{ transform: translateY(-1px); background: rgba(255,255,255,.70); box-shadow: 0 12px 24px rgba(17,24,39,.08); }
    .bb-action-gold{ color: var(--bb-gold) !important; border-color: rgba(201,162,74,.18) !important; }
    .bb-action-ink{ color: rgba(17,24,39,.85) !important; }

    @media (max-width: 768px){
      .bb-hide-md { display:none !important; }
      .bb-show-md { display:block !important; }
    }
    @media (min-width: 769px){
      .bb-show-md { display:none !important; }
    }
  </style>

  {{-- ✅ Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div id="bb-dashboard-header">
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
      <a href="{{ route('admin.citas.create') }}"
         data-bb-modal="1"
         data-title="Registrar cita"
         data-url="{{ route('admin.citas.create') }}"
         class="bb-btn-gold">➕ Registrar cita</a>

      <a href="{{ route('admin.ventas.index') }}"
         data-bb-modal="1"
         data-title="Ventas"
         data-url="{{ route('admin.ventas.index') }}"
         class="bb-btn-ghost">💰 Ver ventas</a>
    </div>
  </div>
  </div>

  {{-- ✅ Host para módulos (SOLO dashboard) --}}
  <div id="bb-module-host" class="mt-6 hidden"></div>

  {{-- ✅ TODO el dashboard aquí adentro: esto es lo que SE OCULTA al abrir un módulo --}}
  <div id="bb-dashboard-only">

    {{-- ✅ Filtro por fecha --}}
    <div class="bb-glass-card px-4 py-3 mb-6">
      <form id="fechaForm" method="GET" action="{{ route('admin.dashboard') }}"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.dashboard', ['fecha' => $prevDate]) }}"
             class="bb-action bb-action-ink" title="Anterior">◀</a>

          <input type="date"
                 name="fecha"
                 class="bb-input"
                 style="max-width: 190px;"
                 value="{{ $fecha->format('Y-m-d') }}"
                 onchange="document.getElementById('fechaForm').submit()">

          <a href="{{ route('admin.dashboard', ['fecha' => $nextDate]) }}"
             class="bb-action bb-action-ink" title="Siguiente">▶</a>

          <a href="{{ route('admin.dashboard', ['fecha' => $todayDate]) }}"
             class="bb-action bb-action-gold" title="Ir a hoy">Hoy</a>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-500">
            Mostrando:
            <span class="font-semibold text-gray-800">{{ $fecha->format('d/m/Y') }}</span>
          </span>
        </div>
      </form>
    </div>

    {{-- ✅ Resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div class="bb-glass-card p-4">
        <div class="flex items-center gap-3">
          <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">💰</span></div>
          <div>
            <p class="text-sm text-gray-500">Generado</p>
            <p class="text-xl font-extrabold bb-gold">${{ number_format($generado ?? 0, 2) }}</p>
          </div>
        </div>
      </div>

      <div class="bb-glass-card p-4">
        <div class="flex items-center gap-3">
          <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">📅</span></div>
          <div>
            <p class="text-sm text-gray-500">Citas del día</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $citasDia ?? 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bb-glass-card p-4">
        <div class="flex items-center gap-3">
          <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">✅</span></div>
          <div>
            <p class="text-sm text-gray-500">Confirmadas</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $confirmadas ?? 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bb-glass-card p-4">
        <div class="flex items-center gap-3">
          <div class="bb-icon-pill" style="width:42px;height:42px;"><span class="text-xl">🏁</span></div>
          <div>
            <p class="text-sm text-gray-500">Completadas</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $completadas ?? 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    @php
      $pillClass = function ($estado) {
        return match($estado) {
          'confirmada'  => 'bb-pill bb-pill-gold',
          'completada'  => 'bb-pill bb-pill-green',
          'cancelada'   => 'bb-pill bb-pill-red',
          default       => 'bb-pill',
        };
      };
    @endphp

    {{-- ✅ Tabla Desktop --}}
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
            @forelse($citas as $cita)
              <tr class="bb-row">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">
                    {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">{{ $cita->cliente_nombre ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $cita->cliente_email ?? '—' }}</div>
                </td>

                <td class="px-4 py-3">
                  <div class="text-sm text-gray-900">{{ $cita->servicios_label ?? '—' }}</div>
                  <div class="text-xs text-gray-500">${{ number_format((float)($cita->servicios_total ?? 0), 2) }}</div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ $cita->empleado_nombre ?? '—' }}</div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <span class="{{ $pillClass($cita->estado_cita ?? null) }}">
                    {{ strtoupper($cita->estado_cita ?? '—') }}
                  </span>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  @if(($cita->venta_total ?? 0) > 0)
                    <span class="bb-pill bb-pill-gold">${{ number_format((float)$cita->venta_total, 2) }}</span>
                  @else
                    <span class="text-xs text-gray-500">Sin venta</span>
                  @endif
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <a class="bb-action bb-action-ink"
                       href="{{ route('admin.citas.show', $cita->id_cita) }}"
                       data-bb-modal="1"
                       data-title="Cita #{{ $cita->id_cita }}"
                       data-url="{{ route('admin.citas.show', $cita->id_cita) }}">👁 Ver</a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                  No hay citas para esta fecha.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ✅ Cards Mobile --}}
    <div class="bb-show-md space-y-3">
      @forelse($citas as $cita)
        <div class="bb-glass-card p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-xs text-gray-500">Hora</div>
              <div class="text-lg font-extrabold text-gray-900">
                {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
              </div>
            </div>
            <span class="{{ $pillClass($cita->estado_cita ?? null) }}">
              {{ strtoupper($cita->estado_cita ?? '—') }}
            </span>
          </div>

          <div class="mt-3 grid grid-cols-1 gap-2">
            <div>
              <div class="text-xs text-gray-500">Cliente</div>
              <div class="text-sm font-semibold text-gray-900">{{ $cita->cliente_nombre ?? '—' }}</div>
              <div class="text-xs text-gray-500">{{ $cita->cliente_email ?? '—' }}</div>
            </div>

            <div>
              <div class="text-xs text-gray-500">Servicio</div>
              <div class="text-sm text-gray-900">{{ $cita->servicios_label ?? '—' }}</div>
              <div class="text-xs text-gray-500">${{ number_format((float)($cita->servicios_total ?? 0), 2) }}</div>
            </div>

            <div>
              <div class="text-xs text-gray-500">Empleado</div>
              <div class="text-sm text-gray-900">{{ $cita->empleado_nombre ?? '—' }}</div>
            </div>

            <div>
              <div class="text-xs text-gray-500">Venta</div>
              @if(($cita->venta_total ?? 0) > 0)
                <div class="text-sm font-semibold bb-gold">${{ number_format((float)$cita->venta_total, 2) }}</div>
              @else
                <div class="text-sm text-gray-500">Sin venta</div>
              @endif
            </div>
          </div>

          <div class="mt-4 flex flex-wrap gap-2">
            <a class="bb-action bb-action-ink"
               href="{{ route('admin.citas.show', $cita->id_cita) }}"
               data-bb-modal="1"
               data-title="Cita #{{ $cita->id_cita }}"
               data-url="{{ route('admin.citas.show', $cita->id_cita) }}">👁 Ver</a>
          </div>
        </div>
      @empty
        <div class="bb-glass-card p-4 text-sm text-gray-500 text-center">
          No hay citas para esta fecha.
        </div>
      @endforelse
    </div>

  </div> {{-- /#bb-dashboard-only --}}
</div>
@endsection
