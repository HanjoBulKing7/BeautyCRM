@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

  {{-- ✅ Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div id="bb-dashboard-header">
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

      {{-- ❌ Quitado: Ver ventas --}}
      {{-- <a href="{{ route('admin.ventas.index') }}" ...>💰 Ver ventas</a> --}}
    </div>
  </div>

  {{-- ✅ Resumen --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;">
          <i class="fas fa-money-bill-wave text-xl bb-gold"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Generado</p>
          <p class="text-xl font-extrabold bb-gold">${{ number_format($generado ?? 0, 2) }}</p>
        </div>
      </div>
    </div>

    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;">
          <i class="fas fa-calendar-check text-xl bb-gold"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Citas del día</p>
          <p class="text-xl font-extrabold text-gray-900">{{ $citasDia ?? 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;">
          <i class="fas fa-circle-check text-xl bb-gold"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Confirmadas</p>
          <p class="text-xl font-extrabold text-gray-900">{{ $confirmadas ?? 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bb-glass-card p-4">
      <div class="flex items-center gap-3">
        <div class="bb-icon-pill" style="width:42px;height:42px;">
          <i class="fas fa-flag-checkered text-xl bb-gold"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Completadas</p>
          <p class="text-xl font-extrabold text-gray-900">{{ $completadas ?? 0 }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ✅ Host para módulos (SOLO dashboard) --}}
  <div id="bb-module-host" class="mt-2 hidden"></div>

  {{-- ✅ Filtro por fecha (MEJORADO) --}}
  <div class="bb-glass-card px-4 py-3 mb-6">
    <form id="fechaForm" method="GET" action="{{ route('admin.dashboard') }}"
          class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.dashboard', ['fecha' => $prevDate]) }}"
           class="bb-action bb-action-ink" title="Anterior">
          <i class="fas fa-chevron-left"></i>
        </a>

        <div class="flex items-center gap-2 bb-date-pill">
          <i class="fas fa-calendar-alt bb-gold"></i>
          <input type="date"
                 name="fecha"
                 class="bb-date-input"
                 value="{{ $fecha->format('Y-m-d') }}"
                 onchange="document.getElementById('fechaForm').submit()">
        </div>

        <a href="{{ route('admin.dashboard', ['fecha' => $nextDate]) }}"
           class="bb-action bb-action-ink" title="Siguiente">
          <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('admin.dashboard', ['fecha' => $todayDate]) }}"
           class="bb-action bb-action-gold" title="Ir a hoy">
          <i class="fas fa-calendar-day"></i> Hoy
        </a>
      </div>

      <div class="flex items-center gap-2">
        <span class="bb-pill bb-pill-soft">
          <i class="fas fa-eye bb-gold"></i>
          Mostrando: <span class="font-extrabold">{{ $fecha->format('d/m/Y') }}</span>
        </span>
      </div>

    </form>
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

    $rowStateClass = function ($estado) {
      return match($estado) {
        'cancelada'   => 'bb-row-state-cancelada',
        'completada'  => 'bb-row-state-completada',
        'confirmada'  => 'bb-row-state-confirmada',
        default       => '',
      };
    };
  @endphp

  {{-- ✅ 2 COLUMNAS: IZQ TABLA/CARDS, DER CALENDARIO --}}
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- =========================
         IZQUIERDA: TABLA + CARDS
         ========================= --}}
    <div class="xl:col-span-7">

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
                <tr class="bb-row {{ $rowStateClass($cita->estado_cita ?? null) }}">
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
                         data-url="{{ route('admin.citas.show', $cita->id_cita) }}">
                         <i class="fas fa-eye"></i> Ver
                      </a>
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
      @if(count($citas) > 0)
      <div class="bb-show-md space-y-3">
        @foreach($citas as $cita)
          <div class="bb-glass-card p-4 {{ $rowStateClass($cita->estado_cita ?? null) }}">
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
                 data-url="{{ route('admin.citas.show', $cita->id_cita) }}">
                 <i class="fas fa-eye"></i> Ver
              </a>
            </div>
          </div>
        @endforeach
      </div>
      @endif

    </div>

    {{-- =========================
         DERECHA: CALENDARIO
         ========================= --}}
    <div class="xl:col-span-5">
      <div class="bb-glass-card p-2 md:p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <i class="fas fa-calendar-days" style="color: rgba(201,162,74,.95)"></i>
              Agenda del salón
            </h2>
            {{-- ❌ Quitado texto instructivo --}}
          </div>

          {{-- ❌ Quitado botón Nueva cita en el calendario --}}
        </div>

        <div class="bb-glass-card overflow-hidden">
          <div class="p-1 md:p-2">
            <div id="citas-calendar" data-events='@json($calendarEvents ?? [])'></div>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /grid --}}

</div>
@endsection

@push('styles')
  <style>
    /* ===== Estados: colores pastel por fila ===== */
    .bb-row-state-cancelada{
      background: rgba(239,68,68,.10);
    }
    .bb-row-state-completada{
      background: rgba(34,197,94,.10);
    }
    .bb-row-state-confirmada{
      background: rgba(234,179,8,.12);
    }

    /* hover conservando el tono */
    .bb-row.bb-row-state-cancelada:hover{ background: rgba(239,68,68,.14) !important; }
    .bb-row.bb-row-state-completada:hover{ background: rgba(34,197,94,.14) !important; }
    .bb-row.bb-row-state-confirmada:hover{ background: rgba(234,179,8,.16) !important; }

    /* Dark mode: que no queme */
    .dark-mode .bb-row-state-cancelada{ background: rgba(239,68,68,.12); }
    .dark-mode .bb-row-state-completada{ background: rgba(34,197,94,.12); }
    .dark-mode .bb-row-state-confirmada{ background: rgba(234,179,8,.14); }

    /* Píldoras si no existían */
    .bb-pill-green{
      background: rgba(34,197,94,.14);
      border: 1px solid rgba(34,197,94,.22);
      color: rgba(17,24,39,.90);
    }
    .bb-pill-red{
      background: rgba(239,68,68,.14);
      border: 1px solid rgba(239,68,68,.22);
      color: rgba(17,24,39,.90);
    }

    /* ===== Barra de fecha (más pro) ===== */
    .bb-date-pill{
      display:inline-flex;
      align-items:center;
      gap:.5rem;
      padding:.45rem .65rem;
      border-radius: 1rem;
      background: rgba(255,255,255,.60);
      border: 1px solid rgba(255,255,255,.65);
      box-shadow: 0 10px 22px rgba(17,24,39,.06);
    }
    .bb-date-input{
      border: 0;
      outline: none;
      background: transparent;
      font-weight: 800;
      color: rgba(17,24,39,.88);
      padding: .15rem .25rem;
    }
    .dark-mode .bb-date-pill{
      background: rgba(17,24,39,.35);
      border-color: rgba(255,255,255,.10);
      box-shadow: 0 10px 22px rgba(0,0,0,.25);
    }
    .dark-mode .bb-date-input{ color: rgba(249,250,251,.92); }

    .bb-pill-soft{
      background: rgba(255,255,255,.60);
      border: 1px solid rgba(255,255,255,.65);
    }
    .dark-mode .bb-pill-soft{
      background: rgba(17,24,39,.35);
      border-color: rgba(255,255,255,.10);
    }

    /* ===== FullCalendar custom styles (tuyo + más fino) ===== */
    .fc .fc-button{
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95)) !important;
      color:#111827 !important;
      border: 1px solid rgba(201,162,74,.35) !important;
      box-shadow: 0 10px 22px rgba(201,162,74,.12) !important;
      font-weight: 800 !important;
      border-radius: .95rem !important;

      /* ✅ más pequeño y fino */
      padding: .30rem .55rem !important;
      font-size: .78rem !important;
      line-height: 1 !important;
    }
    .fc .fc-button:hover{
      background: linear-gradient(135deg, rgba(201,162,74,1), rgba(231,215,161,1)) !important;
    }
    .fc .fc-button:focus{
      box-shadow: 0 0 0 3px rgba(201,162,74,.22) !important;
      outline: none !important;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active{
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95)) !important;
      color:#111827 !important;
      border-color: rgba(201,162,74,.35) !important;
      box-shadow: 0 12px 28px rgba(201,162,74,.18) !important;
    }
    .fc .fc-button .fc-icon{ font-size: .95em !important; }
    .fc .fc-button-group .fc-button{ margin: 0 .15rem !important; }

    .fc .fc-toolbar{ gap: .5rem !important; }
    .fc .fc-toolbar-title{
      font-weight: 900 !important;
      color: rgba(17,24,39,.88) !important;
      letter-spacing: .2px;
      font-size: 1rem !important;
    }

    .fc .fc-scrollgrid, .fc .fc-scrollgrid table{ border-color: rgba(17,24,39,.08) !important; }
    .fc .fc-col-header-cell-cushion{
      color: rgba(17,24,39,.70) !important;
      font-weight: 800 !important;
      text-decoration:none !important;
      font-size: .80rem !important;
    }
    .fc .fc-daygrid-day-number{
      color: rgba(17,24,39,.70) !important;
      text-decoration:none !important;
      font-weight: 700 !important;
      font-size: .80rem !important;
    }
    .fc .fc-daygrid-event{
      border-radius: .8rem !important;
      border: 1px solid rgba(201,162,74,.20) !important;
      background: rgba(201,162,74,.12) !important;
      color: rgba(17,24,39,.88) !important;
      padding: .15rem .35rem !important;
      font-weight: 700 !important;
    }
    .fc .fc-day-today{ background: rgba(201,162,74,.10) !important; }

    .dark-mode .fc .fc-button{
      background: rgba(17,24,39,.35) !important;
      border-color: rgba(255,255,255,.10) !important;
      color: rgba(249,250,251,.92) !important;
      box-shadow: 0 10px 22px rgba(0,0,0,.25) !important;
    }
    .dark-mode .fc .fc-button:hover{ background: rgba(17,24,39,.55) !important; }
    .dark-mode .fc .fc-toolbar-title{ color: rgba(249,250,251,.95) !important; }
    .dark-mode .fc .fc-scrollgrid, .dark-mode .fc .fc-scrollgrid table{ border-color: rgba(255,255,255,.10) !important; }
    .dark-mode .fc .fc-col-header-cell-cushion,
    .dark-mode .fc .fc-daygrid-day-number{ color: rgba(229,231,235,.85) !important; }
    .dark-mode .fc .fc-daygrid-event{
      background: rgba(201,162,74,.14) !important;
      border-color: rgba(201,162,74,.22) !important;
      color: rgba(249,250,251,.92) !important;
    }
    .dark-mode .fc .fc-day-today{ background: rgba(201,162,74,.08) !important; }

    /* Móvil: toolbar compacta */
    @media (max-width: 640px){
      .fc .fc-toolbar{
        flex-direction: column !important;
        align-items: stretch !important;
      }
      .fc .fc-toolbar-chunk{
        display:flex !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: .35rem !important;
      }
    }
  </style>
@endpush

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const calendarEl = document.getElementById('citas-calendar');
      if (!calendarEl) return;

      const events = @json($calendarEvents ?? []);

      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        firstDay: 1,
        initialView: 'dayGridMonth',
        height: 'auto',
        nowIndicator: true,
        selectable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          list: 'Agenda'
        },
        events,

        dateClick: (info) => {
          const url = new URL(`{{ route('admin.citas.create') }}`, window.location.origin);
          url.searchParams.set('date', info.dateStr);
          window.location.href = url.toString();
        },

        eventClick: (info) => {
          window.location.href = `{{ url('/admin/citas') }}/${info.event.id}/edit`;
        },

        eventMouseEnter(info) {
          const title = info.event.title;
          const start = info.event.start;
          if (!start) return;

          const fecha = start.toLocaleDateString('es-MX', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          });

          const hora = start.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
          });

          info.el.title = `${title} - ${fecha} ${hora}`;
        },
      });

      calendar.render();
    });
  </script>
@endpush