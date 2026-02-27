@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

  {{-- ✅ Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div id="bb-dashboard-header">
      <h1 class="text-2xl font-extrabold text-gray-800 flex items-center gap-3 dark:text-gray-100">
        <span class="bb-icon-pill">
          <i class="fas fa-layer-group text-xl bb-gold"></i>
        </span>
        Dashboard de Citas
      </h1>
      <p class="text-sm text-gray-500 mt-1 ml-14 dark:text-gray-400">Control general de tu agenda y ventas del día.</p>
    </div>

    <div class="flex gap-2 sm:justify-end">
      <a href="{{ route('admin.citas.create') }}"
         data-bb-modal="1"
         data-title="Registrar cita"
         data-url="{{ route('admin.citas.create') }}"
         class="bb-btn-gold shadow-lg hover:shadow-xl transition-shadow">
         <i class="fas fa-plus"></i> Nueva Cita
      </a>
    </div>
  </div>

  {{-- ✅ Resumen (Tarjetas) --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    {{-- Generado --}}
    <div class="bb-glass-card p-5 flex items-center gap-4 transition-transform hover:-translate-y-1">
      <div class="bb-icon-pill bg-white shadow-sm" style="width:48px;height:48px;">
        <i class="fas fa-wallet text-2xl bb-gold"></i>
      </div>
      <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Generado</p>
        <p class="text-2xl font-black bb-gold">${{ number_format($generado ?? 0, 2) }}</p>
      </div>
    </div>

    {{-- Citas del día --}}
    <div class="bb-glass-card p-5 flex items-center gap-4 transition-transform hover:-translate-y-1">
      <div class="bb-icon-pill bg-white shadow-sm" style="width:48px;height:48px;">
        <i class="fas fa-calendar-day text-2xl text-blue-500"></i>
      </div>
      <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Citas del día</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $citasDia ?? 0 }}</p>
      </div>
    </div>

    {{-- Confirmadas --}}
    <div class="bb-glass-card p-5 flex items-center gap-4 transition-transform hover:-translate-y-1">
      <div class="bb-icon-pill bg-white shadow-sm" style="width:48px;height:48px;">
        <i class="fas fa-user-check text-2xl text-amber-500"></i>
      </div>
      <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Confirmadas</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $confirmadas ?? 0 }}</p>
      </div>
    </div>

    {{-- Completadas --}}
    <div class="bb-glass-card p-5 flex items-center gap-4 transition-transform hover:-translate-y-1">
      <div class="bb-icon-pill bg-white shadow-sm" style="width:48px;height:48px;">
        <i class="fas fa-clipboard-check text-2xl text-green-500"></i>
      </div>
      <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Completadas</p>
        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $completadas ?? 0 }}</p>
      </div>
    </div>
  </div>

  {{-- ✅ Host para módulos (SOLO dashboard) --}}
  <div id="bb-module-host" class="mt-2 hidden"></div>

  @php
    $pillClass = function ($estado) {
      return match($estado) {
        'confirmada'  => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/30',
        'completada'  => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800/30',
        'cancelada'   => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/30',
        default       => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
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
         IZQUIERDA: CONTROL DE FECHA Y TABLA
         ========================= --}}
    <div class="xl:col-span-7 flex flex-col gap-6">

      {{-- Control de Fecha Integrado --}}
      <div class="bb-glass-card px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white">Agenda Diaria</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Mostrando: <strong class="text-gray-800 dark:text-gray-200">{{ $fecha->translatedFormat('l, d M Y') }}</strong></p>
          </div>
        </div>

        <form id="fechaForm" method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900/50 p-1.5 rounded-xl border border-gray-200 dark:border-gray-700">
          <a href="{{ route('admin.dashboard', ['fecha' => $prevDate]) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm dark:hover:bg-gray-800 transition-all text-gray-500" title="Día Anterior">
            <i class="fas fa-chevron-left"></i>
          </a>
          
          <input type="date" name="fecha" class="bg-transparent border-none text-sm font-bold text-gray-800 dark:text-gray-200 focus:ring-0 cursor-pointer text-center w-[130px]" value="{{ $fecha->format('Y-m-d') }}" onchange="document.getElementById('fechaForm').submit()">
          
          <a href="{{ route('admin.dashboard', ['fecha' => $nextDate]) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm dark:hover:bg-gray-800 transition-all text-gray-500" title="Día Siguiente">
            <i class="fas fa-chevron-right"></i>
          </a>

          <div class="w-px h-5 bg-gray-300 dark:bg-gray-700 mx-1"></div>
          
          <a href="{{ route('admin.dashboard', ['fecha' => $todayDate]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shadow-sm" title="Ir a hoy">
            Hoy
          </a>
        </form>
      </div>

      {{-- ✅ Tabla Desktop --}}
      <div class="bb-glass-card overflow-hidden bb-hide-md flex-1">
        <div class="overflow-x-auto">
          <table class="min-w-full text-left">
            <thead class="bb-thead">
              <tr>
                <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Hora</th>
                <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Cliente / Servicio</th>
                <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
              @forelse($citas as $cita)
                <tr class="bb-row {{ $rowStateClass($cita->estado_cita ?? null) }} group">
                  <td class="px-5 py-4 whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-900 dark:text-gray-100 bg-white/60 dark:bg-gray-800/60 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                      <i class="far fa-clock text-gray-400 text-xs"></i>
                      {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
                    </div>
                  </td>

                  <td class="px-5 py-4">
                    <div class="flex flex-col gap-1">
                      <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $cita->cliente_nombre ?? '—' }}</div>
                      <div class="text-xs text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-cut text-gray-400"></i> {{ $cita->servicios_label ?? '—' }}
                      </div>
                      <div class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                        <i class="fas fa-user-tie"></i> {{ $cita->empleado_nombre ?? '—' }}
                      </div>
                    </div>
                  </td>

                  <td class="px-5 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $pillClass($cita->estado_cita ?? null) }}">
                      {{ $cita->estado_cita ?? '—' }}
                    </span>
                    @if(($cita->venta_total ?? 0) > 0)
                      <div class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded border border-green-100 dark:border-green-800/30">
                        <i class="fas fa-check"></i> Pagado: ${{ number_format((float)$cita->venta_total, 2) }}
                      </div>
                    @endif
                  </td>

                  <td class="px-5 py-4 whitespace-nowrap text-right">
                    <a class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors shadow-sm"
                       href="{{ route('admin.citas.show', $cita->id_cita) }}"
                       data-bb-modal="1"
                       data-title="Cita #{{ $cita->id_cita }}"
                       data-url="{{ route('admin.citas.show', $cita->id_cita) }}">
                       <i class="far fa-eye text-blue-500"></i> Ver
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-5 py-12 text-center">
                     <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800/50 text-gray-400 mb-4">
                        <i class="fas fa-mug-hot text-2xl"></i>
                     </div>
                     <p class="font-bold text-gray-800 dark:text-gray-200">Día libre de citas</p>
                     <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No hay citas registradas para esta fecha.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- ✅ Cards Mobile --}}
      @if(count($citas) > 0)
      <div class="bb-show-md space-y-4">
        @foreach($citas as $cita)
          <div class="bb-glass-card p-4 relative overflow-hidden {{ $rowStateClass($cita->estado_cita ?? null) }}">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 dark:border-gray-700/50 pb-3 mb-3">
              <div class="flex items-center gap-2">
                <i class="far fa-clock text-blue-500"></i>
                <span class="text-lg font-black text-gray-900 dark:text-white">
                  {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
                </span>
              </div>
              <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $pillClass($cita->estado_cita ?? null) }}">
                {{ $cita->estado_cita ?? '—' }}
              </span>
            </div>

            <div class="space-y-2">
              <div class="flex items-start gap-2">
                <i class="fas fa-user text-gray-400 mt-0.5 text-xs w-4"></i>
                <div>
                  <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $cita->cliente_nombre ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $cita->cliente_email ?? '—' }}</div>
                </div>
              </div>

              <div class="flex items-start gap-2">
                <i class="fas fa-cut text-gray-400 mt-0.5 text-xs w-4"></i>
                <div class="text-sm text-gray-700 dark:text-gray-300">{{ $cita->servicios_label ?? '—' }}</div>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
              @if(($cita->venta_total ?? 0) > 0)
                <div class="text-sm font-bold bb-gold flex items-center gap-1"><i class="fas fa-check-circle"></i> ${{ number_format((float)$cita->venta_total, 2) }}</div>
              @else
                <div class="text-xs text-gray-400 italic">Sin venta</div>
              @endif
              
              <a class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-lg transition-colors"
                 href="{{ route('admin.citas.show', $cita->id_cita) }}">
                 <i class="far fa-eye text-blue-500"></i> Ver
              </a>
            </div>
          </div>
        @endforeach
      </div>
      @endif

    </div>

    {{-- =========================
         DERECHA: CALENDARIO MEJORADO
         ========================= --}}
    <div class="xl:col-span-5">
      <div class="bb-glass-card p-4 h-full flex flex-col">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
          <i class="fas fa-calendar-alt text-blue-500"></i>
          Vista Mensual
        </h2>

        <div class="flex-1 bg-white/50 dark:bg-gray-900/30 rounded-xl p-2 md:p-3 border border-gray-100 dark:border-gray-800/50">
          <div id="citas-calendar" data-events='@json($calendarEvents ?? [])'></div>
        </div>
      </div>
    </div>

  </div>{{-- /grid --}}

</div>
@endsection

@push('styles')
  <style>
    /* ===== Estados: colores pastel por fila ===== */
    .bb-row-state-cancelada{ background: rgba(239,68,68,.05); }
    .bb-row-state-completada{ background: rgba(34,197,94,.05); }
    .bb-row-state-confirmada{ background: rgba(245,158,11,.05); }

    .bb-row.bb-row-state-cancelada:hover{ background: rgba(239,68,68,.10) !important; }
    .bb-row.bb-row-state-completada:hover{ background: rgba(34,197,94,.10) !important; }
    .bb-row.bb-row-state-confirmada:hover{ background: rgba(245,158,11,.10) !important; }

    .dark-mode .bb-row-state-cancelada{ background: rgba(239,68,68,.10); }
    .dark-mode .bb-row-state-completada{ background: rgba(34,197,94,.10); }
    .dark-mode .bb-row-state-confirmada{ background: rgba(245,158,11,.10); }

    /* ===== CALENDARIO FULLCALENDAR: REDISEÑO CLEAN & INTUITIVO ===== */
    /* Botones de navegación y vistas */
    .fc .fc-button-primary {
      background: #ffffff !important;
      border: 1px solid #e5e7eb !important;
      color: #4b5563 !important;
      border-radius: 0.5rem !important;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
      text-transform: capitalize;
      font-weight: 600 !important;
      padding: 0.4rem 0.75rem !important;
      transition: all 0.2s ease;
    }
    .fc .fc-button-primary:hover {
      background: #f9fafb !important;
      color: #111827 !important;
      border-color: #d1d5db !important;
    }
    /* Estado activo del botón (El único que usa el dorado/azul) */
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
      background: rgba(201,162,74,0.1) !important;
      border-color: rgba(201,162,74,0.4) !important;
      color: rgba(201,162,74,1) !important;
      box-shadow: none !important;
    }
    .fc .fc-button-primary:focus {
      box-shadow: 0 0 0 2px rgba(201,162,74,0.2) !important;
    }

    /* Quitar bordes feos internos */
    .fc .fc-scrollgrid { border: none !important; }
    .fc .fc-scrollgrid td, .fc .fc-scrollgrid th { border-color: #f3f4f6 !important; }
    
    /* Cabecera del calendario (Mes/Año) */
    .fc .fc-toolbar-title {
      font-weight: 800 !important;
      font-size: 1.15rem !important;
      color: #1f2937 !important;
    }
    
    /* Días de la semana (L, M, M, J...) */
    .fc .fc-col-header-cell-cushion {
      color: #6b7280 !important;
      font-weight: 700 !important;
      text-transform: uppercase;
      font-size: 0.75rem !important;
      padding: 0.5rem 0 !important;
    }

    /* Número del día */
    .fc .fc-daygrid-day-number {
      color: #374151 !important;
      font-weight: 600 !important;
      font-size: 0.85rem !important;
      margin: 2px;
    }

    /* Highlight del Día de hoy */
    .fc .fc-day-today {
      background: rgba(59, 130, 246, 0.05) !important; /* Fondo sutil azul */
    }
    .fc .fc-day-today .fc-daygrid-day-number {
      background: #3b82f6 !important;
      color: white !important;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    /* Píldoras de Eventos (Citas en el calendario) */
    .fc .fc-daygrid-event {
      border-radius: 4px !important;
      border: none !important;
      background: rgba(201,162,74,0.15) !important;
      color: #927429 !important; /* Dorado oscuro para buen contraste */
      font-weight: 700 !important;
      font-size: 0.75rem !important;
      padding: 2px 4px !important;
      margin-top: 1px !important;
      cursor: pointer;
      transition: transform 0.1s ease;
    }
    .fc .fc-daygrid-event:hover {
      transform: scale(1.02);
      filter: brightness(0.95);
    }

    /* Dark Mode Calendar Adaptations */
    .dark .fc .fc-button-primary {
      background: rgba(31,41,55,1) !important;
      border-color: rgba(75,85,99,1) !important;
      color: #d1d5db !important;
    }
    .dark .fc .fc-button-primary:hover { background: rgba(55,65,81,1) !important; }
    .dark .fc .fc-toolbar-title { color: #f9fafb !important; }
    .dark .fc .fc-scrollgrid td, .dark .fc .fc-scrollgrid th { border-color: rgba(55,65,81,0.5) !important; }
    .dark .fc .fc-col-header-cell-cushion { color: #9ca3af !important; }
    .dark .fc .fc-daygrid-day-number { color: #e5e7eb !important; }
    .dark .fc .fc-day-today { background: rgba(59, 130, 246, 0.1) !important; }
    .dark .fc .fc-daygrid-event {
      background: rgba(201,162,74,0.2) !important;
      color: #e2c67b !important;
    }

    /* Móvil: toolbar compacta */
    @media (max-width: 640px){
      .fc .fc-toolbar { flex-direction: column !important; gap: 0.5rem !important; }
      .fc .fc-toolbar-chunk { display:flex !important; justify-content: center !important; flex-wrap: wrap !important; }
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
        dayMaxEvents: 3, // Evita que se estire demasiado la celda, muestra "ver más"
        headerToolbar: {
          left: 'prev,next',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
        },
        events,

        dateClick: (info) => {
          // Redirigir al creador de citas seleccionando el día
          const url = new URL(`{{ route('admin.citas.create') }}`, window.location.origin);
          url.searchParams.set('date', info.dateStr);
          window.location.href = url.toString();
        },

        eventClick: (info) => {
          // Redirigir al modo edición/vista de esa cita específica
          window.location.href = `{{ url('/admin/citas') }}/${info.event.id}/edit`;
        },

        eventMouseEnter(info) {
          const title = info.event.title;
          const start = info.event.start;
          if (!start) return;

          const fecha = start.toLocaleDateString('es-MX', {
            weekday: 'short',
            day: 'numeric',
            month: 'short'
          });

          const hora = start.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
          });

          // Un tooltip nativo limpio
          info.el.title = `Cita: ${title}\nHorario: ${fecha} a las ${hora}`;
        },
      });

      calendar.render();
    });
  </script>
@endpush