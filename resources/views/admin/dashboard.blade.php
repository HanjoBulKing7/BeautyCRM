@extends('layouts.app')

@section('content')
<style>
    /* Efecto Glass Premium */
    .bb-glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    
    /* Botones de acción suaves */
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        transition: all 0.2s ease;
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl">

  {{-- ✅ Header --}}
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-[rgba(201,162,74,0.1)] text-[rgba(201,162,74,1)] flex items-center justify-center shadow-sm shrink-0">
          <i class="fas fa-layer-group text-xl"></i>
        </div>
        Dashboard de Citas
      </h1>
      <p class="text-sm text-gray-500 mt-1 ml-15">Control general de tu agenda y ventas del día.</p>
    </div>

    <div class="flex gap-2 sm:justify-end">
      <a href="{{ route('admin.citas.create') }}"
         data-bb-modal="1"
         data-title="Registrar cita"
         data-url="{{ route('admin.citas.create') }}"
         class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gray-900 text-white font-bold hover:bg-black shadow-lg hover:shadow-xl transition-all w-full md:w-auto">
         <i class="fas fa-plus"></i> Nueva Cita
      </a>
    </div>
  </div>

  {{-- ✅ Resumen (Tarjetas) --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    {{-- Generado --}}
    <div class="bb-glass-card rounded-[2rem] p-6 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-lg">
      <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0 border border-gray-50">
        <i class="fas fa-wallet text-2xl text-[rgba(201,162,74,1)]"></i>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Generado</p>
        <p class="text-2xl font-black text-[rgba(201,162,74,1)]">${{ number_format($generado ?? 0, 2) }}</p>
      </div>
    </div>

    {{-- Citas del día --}}
    <div class="bb-glass-card rounded-[2rem] p-6 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-lg">
      <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0 border border-gray-50">
        <i class="fas fa-calendar-day text-2xl text-blue-500"></i>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Citas del día</p>
        <p class="text-2xl font-black text-gray-900">{{ $citasDia ?? 0 }}</p>
      </div>
    </div>

    {{-- Confirmadas --}}
    <div class="bb-glass-card rounded-[2rem] p-6 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-lg">
      <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0 border border-gray-50">
        <i class="fas fa-user-check text-2xl text-amber-500"></i>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Confirmadas</p>
        <p class="text-2xl font-black text-gray-900">{{ $confirmadas ?? 0 }}</p>
      </div>
    </div>

    {{-- Completadas --}}
    <div class="bb-glass-card rounded-[2rem] p-6 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-lg">
      <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0 border border-gray-50">
        <i class="fas fa-clipboard-check text-2xl text-green-500"></i>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Completadas</p>
        <p class="text-2xl font-black text-gray-900">{{ $completadas ?? 0 }}</p>
      </div>
    </div>
  </div>

  {{-- ✅ Host para módulos (SOLO dashboard) --}}
  <div id="bb-module-host" class="mt-2 hidden"></div>

  @php
    $pillClass = function ($estado) {
      return match($estado) {
        'confirmada'  => 'bg-amber-50 text-amber-600 border border-amber-200/50',
        'completada'  => 'bg-green-50 text-green-600 border border-green-200/50',
        'cancelada'   => 'bg-red-50 text-red-600 border border-red-200/50',
        default       => 'bg-gray-50 text-gray-600 border border-gray-200/50',
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
      <div class="bb-glass-card rounded-[2rem] px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-50/50 flex items-center justify-center text-blue-500 border border-blue-100/50">
            <i class="fas fa-calendar-day text-lg"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-gray-900">Agenda Diaria</h3>
            <p class="text-xs text-gray-500 mt-0.5">Mostrando: <strong class="text-gray-800">{{ $fecha->translatedFormat('l, d M Y') }}</strong></p>
          </div>
        </div>

        <form id="fechaForm" method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 p-1.5 rounded-2xl bg-white/40 border border-white shadow-sm">
          <a href="{{ route('admin.dashboard', ['fecha' => $prevDate]) }}" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white text-gray-500 transition-all shadow-sm" title="Día Anterior">
            <i class="fas fa-chevron-left text-sm"></i>
          </a>
          
          <input type="date" name="fecha" class="bg-transparent border-none text-sm font-bold text-gray-800 focus:ring-0 cursor-pointer text-center w-[130px] p-0" value="{{ $fecha->format('Y-m-d') }}" onchange="document.getElementById('fechaForm').submit()">
          
          <a href="{{ route('admin.dashboard', ['fecha' => $nextDate]) }}" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white text-gray-500 transition-all shadow-sm" title="Día Siguiente">
            <i class="fas fa-chevron-right text-sm"></i>
          </a>

          <div class="w-px h-6 bg-gray-200/60 mx-1"></div>
          
          <a href="{{ route('admin.dashboard', ['fecha' => $todayDate]) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-gray-700 hover:text-blue-600 transition-colors shadow-sm" title="Ir a hoy">
            Hoy
          </a>
        </form>
      </div>

      {{-- ✅ Tabla Desktop --}}
      <div class="hidden md:block bb-glass-card rounded-[2.5rem] overflow-hidden flex-1">
        <div class="overflow-x-auto p-6">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-gray-200/60">
                <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Hora</th>
                <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Cliente / Servicio</th>
                <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Estado</th>
                <th class="pb-4 px-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Acciones</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100/60">
              @forelse($citas as $cita)
                <tr class="hover:bg-white/40 transition-colors group">
                  <td class="py-4 px-4 whitespace-nowrap">
                    <div class="inline-flex items-center gap-2 text-sm font-black text-gray-900 bg-white/80 px-3 py-1.5 rounded-xl border border-white shadow-sm">
                      <i class="far fa-clock text-blue-500"></i>
                      {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
                    </div>
                  </td>

                  <td class="py-4 px-4">
                    <div class="flex flex-col gap-1">
                      <div class="text-sm font-black text-gray-900">{{ $cita->cliente_nombre ?? '—' }}</div>
                      <div class="text-xs font-medium text-gray-600 flex items-center gap-1.5">
                        <i class="fas fa-cut text-gray-400 w-3"></i> {{ $cita->servicios_label ?? '—' }}
                      </div>
                      <div class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                        <i class="fas fa-user-tie w-3"></i> {{ $cita->empleado_nombre ?? '—' }}
                      </div>
                    </div>
                  </td>

                  <td class="py-4 px-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $pillClass($cita->estado_cita ?? null) }}">
                      {{ $cita->estado_cita ?? '—' }}
                    </span>
                    @if(($cita->venta_total ?? 0) > 0)
                      <div class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-green-600 bg-green-50/50 px-2 py-1 rounded-md border border-green-100/50">
                        <i class="fas fa-check"></i> Pagado: ${{ number_format((float)$cita->venta_total, 2) }}
                      </div>
                    @endif
                  </td>

                  <td class="py-4 px-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end opacity-80 group-hover:opacity-100 transition-opacity">
                        <a class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100"
                           href="{{ route('admin.citas.show', $cita->id_cita) }}"
                           data-bb-modal="1"
                           data-title="Cita #{{ $cita->id_cita }}"
                           data-url="{{ route('admin.citas.show', $cita->id_cita) }}"
                           title="Ver detalle">
                           <i class="far fa-eye"></i>
                        </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="py-12 text-center">
                     <div class="w-20 h-20 mx-auto rounded-full bg-white flex items-center justify-center text-3xl mb-4 shadow-sm border border-gray-50">☕</div>
                     <p class="font-bold text-lg text-gray-900">Día libre de citas</p>
                     <p class="text-sm text-gray-500 mt-1">No hay citas registradas para esta fecha.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- ✅ Cards Mobile --}}
      @if(count($citas) > 0)
      <div class="md:hidden space-y-4">
        @foreach($citas as $cita)
          <div class="bb-glass-card rounded-[2rem] p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100/60 pb-3 mb-3">
              <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-white/80 shadow-sm flex items-center justify-center text-blue-500">
                    <i class="far fa-clock"></i>
                </div>
                <span class="text-lg font-black text-gray-900">
                  {{ is_string($cita->hora_cita ?? null) ? substr($cita->hora_cita, 0, 5) : '—' }}
                </span>
              </div>
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $pillClass($cita->estado_cita ?? null) }}">
                {{ $cita->estado_cita ?? '—' }}
              </span>
            </div>

            <div class="space-y-2.5 pl-1">
              <div class="flex items-start gap-3">
                <i class="fas fa-user text-gray-400 mt-1 text-xs w-4"></i>
                <div>
                  <div class="text-sm font-black text-gray-900">{{ $cita->cliente_nombre ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $cita->cliente_email ?? '—' }}</div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <i class="fas fa-cut text-[rgba(201,162,74,1)] mt-1 text-xs w-4"></i>
                <div class="text-sm font-medium text-gray-600">{{ $cita->servicios_label ?? '—' }}</div>
              </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100/60 flex items-center justify-between">
              @if(($cita->venta_total ?? 0) > 0)
                <div class="text-sm font-black text-green-600 flex items-center gap-1">
                    <i class="fas fa-check-circle"></i> ${{ number_format((float)$cita->venta_total, 2) }}
                </div>
              @else
                <div class="text-[11px] font-medium text-gray-400 uppercase tracking-widest">Sin venta</div>
              @endif
              
              <a class="action-btn text-blue-500 bg-blue-50 hover:bg-blue-100"
                 href="{{ route('admin.citas.show', $cita->id_cita) }}">
                 <i class="far fa-eye"></i>
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
      <div class="bb-glass-card rounded-[2.5rem] p-6 h-full flex flex-col">
        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3 mb-6">
          <div class="w-10 h-10 rounded-xl bg-blue-50/50 flex items-center justify-center text-blue-500 border border-blue-100/50">
            <i class="fas fa-calendar-alt"></i>
          </div>
          Vista Mensual
        </h2>

        <div class="flex-1 bg-white/40 rounded-3xl p-4 border border-white shadow-sm">
          <div id="citas-calendar" data-events='@json($calendarEvents ?? [])'></div>
        </div>
      </div>
    </div>

  </div>{{-- /grid --}}

</div>
@endsection

@push('styles')
  <style>
    /* ===== CALENDARIO FULLCALENDAR: GLASSMORPHISM CLEAN ===== */
    /* Botones de navegación y vistas */
    .fc .fc-button-primary {
      background: rgba(255,255,255,0.7) !important;
      border: 1px solid rgba(255,255,255,0.9) !important;
      color: #4b5563 !important;
      border-radius: 0.75rem !important;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
      text-transform: capitalize;
      font-weight: 700 !important;
      padding: 0.4rem 0.8rem !important;
      transition: all 0.2s ease;
      backdrop-filter: blur(4px);
    }
    .fc .fc-button-primary:hover {
      background: #ffffff !important;
      color: #111827 !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Estado activo del botón */
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
      background: rgba(201,162,74,0.1) !important;
      border-color: rgba(201,162,74,0.3) !important;
      color: rgba(201,162,74,1) !important;
      box-shadow: none !important;
    }
    .fc .fc-button-primary:focus {
      box-shadow: 0 0 0 2px rgba(201,162,74,0.2) !important;
    }

    /* Bordes sutiles de la tabla del calendario */
    .fc .fc-scrollgrid { border: none !important; }
    .fc .fc-scrollgrid td, .fc .fc-scrollgrid th { border-color: rgba(0,0,0,0.03) !important; }
    
    /* Cabecera del calendario (Mes/Año) */
    .fc .fc-toolbar-title {
      font-weight: 900 !important;
      font-size: 1.25rem !important;
      color: #1f2937 !important;
    }
    
    /* Días de la semana (L, M, M, J...) */
    .fc .fc-col-header-cell-cushion {
      color: #9ca3af !important;
      font-weight: 800 !important;
      text-transform: uppercase;
      font-size: 0.7rem !important;
      letter-spacing: 0.05em;
      padding: 0.5rem 0 !important;
    }

    /* Número del día */
    .fc .fc-daygrid-day-number {
      color: #4b5563 !important;
      font-weight: 700 !important;
      font-size: 0.85rem !important;
      margin: 4px;
    }

    /* Highlight del Día de hoy */
    .fc .fc-day-today {
      background: rgba(255, 255, 255, 0.4) !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
      background: #3b82f6 !important;
      color: white !important;
      border-radius: 50%;
      width: 26px;
      height: 26px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    /* Píldoras de Eventos (Citas en el calendario) */
    .fc .fc-daygrid-event {
      border-radius: 6px !important;
      border: 1px solid rgba(201,162,74,0.2) !important;
      background: rgba(201,162,74,0.08) !important;
      color: rgba(201,162,74,1) !important;
      font-weight: 800 !important;
      font-size: 0.7rem !important;
      padding: 2px 6px !important;
      margin-top: 2px !important;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .fc .fc-daygrid-event:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(201,162,74,0.15);
      background: rgba(201,162,74,0.15) !important;
    }

    /* Eliminar el fondo azul feo al hacer clic en un día vacío */
    .fc .fc-highlight {
        background: rgba(201,162,74,0.05) !important;
    }

    /* Móvil: toolbar compacta */
    @media (max-width: 640px){
      .fc .fc-toolbar { flex-direction: column !important; gap: 1rem !important; }
      .fc .fc-toolbar-chunk { display:flex !important; justify-content: center !important; flex-wrap: wrap !important; gap: 0.5rem; }
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
        dayMaxEvents: 3,
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
            day: 'numeric',
            month: 'short'
          });

          const hora = start.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
          });

          info.el.title = `Cita: ${title}\nHorario: ${fecha} a las ${hora}`;
        },
      });

      calendar.render();
    });
  </script>
@endpush