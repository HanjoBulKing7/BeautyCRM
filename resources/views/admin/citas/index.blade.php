@extends('layouts.app')

@section('title', 'Calendario de Citas')
@section('page-title', 'Citas')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

  <style>
    /* =========================================================
       FULLCALENDAR CUSTOM STYLES (Gold UI - Beauty Bonita)
       ========================================================= */

    :root{
      --bb-gold: #c9a24a;        /* Dorado base */
      --bb-gold-2: #b88a2f;      /* Dorado oscuro */
      --bb-gold-soft: rgba(201,162,74,0.12);
      --bb-gold-border: rgba(201,162,74,0.35);
      --bb-text: #1f2937;
    }

    .fc { font-family: inherit; }

    /* Botones de navegación y vistas */
    .fc .fc-button-primary {
      background: #ffffff !important;
      border: 1px solid #e5e7eb !important;
      color: #4b5563 !important;
      border-radius: 0.5rem !important;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
      text-transform: capitalize;
      font-weight: 600 !important;
      padding: 0.4rem 0.85rem !important;
      transition: all 0.2s ease;
      font-size: 0.875rem !important;
    }
    .fc .fc-button-primary:hover {
      background: #f9fafb !important;
      color: #111827 !important;
      border-color: #d1d5db !important;
    }

    /* Estado activo del botón (Dorado) */
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
      background: var(--bb-gold-soft) !important;
      border-color: var(--bb-gold-border) !important;
      color: var(--bb-gold) !important;
      box-shadow: none !important;
    }
    .fc .fc-button-primary:focus {
      box-shadow: 0 0 0 2px rgba(201, 162, 74, 0.22) !important;
    }

    /* Cabecera del calendario (Mes/Año) */
    .fc .fc-toolbar-title {
      font-weight: 800 !important;
      font-size: 1.25rem !important;
      color: #1f2937 !important;
      letter-spacing: -0.025em;
    }

    /* Quitar bordes gruesos internos */
    .fc .fc-scrollgrid { border: 1px solid #f3f4f6 !important; border-radius: 0.5rem; overflow: hidden; }
    .fc .fc-scrollgrid td, .fc .fc-scrollgrid th { border-color: #f3f4f6 !important; }

    /* Días de la semana (L, M, M, J...) */
    .fc .fc-col-header-cell-cushion {
      color: #6b7280 !important;
      font-weight: 700 !important;
      text-transform: uppercase;
      font-size: 0.75rem !important;
      padding: 0.75rem 0 !important;
      letter-spacing: 0.05em;
    }

    /* Número del día */
    .fc .fc-daygrid-day-number {
      color: #374151 !important;
      font-weight: 600 !important;
      font-size: 0.85rem !important;
      margin: 4px;
      text-decoration: none !important;
    }

    /* Highlight del Día de hoy */
    .fc .fc-day-today {
      background: rgba(201, 162, 74, 0.03) !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
      background: #c9a24a !important; /* Dorado base */
      color: #111827 !important;
      border-radius: 50%;
      width: 28px;
      height: 28px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 18px rgba(201,162,74,0.25);
    }

    /* Píldoras de Eventos (Citas) */
    .fc .fc-daygrid-event {
      border-radius: 6px !important;
      border: 1px solid rgba(201, 162, 74, 0.35) !important;
      background: rgba(201, 162, 74, 0.14) !important;
      color: rgba(143, 102, 18, 1) !important;
      font-weight: 700 !important;
      font-size: 0.75rem !important;
      padding: 3px 6px !important;
      margin: 2px 4px !important;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .fc .fc-daygrid-event:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      background: rgba(201, 162, 74, 0.20) !important;
    }

    /* Eliminar el punto predeterminado en la vista mensual */
    .fc-daygrid-event-dot { display: none !important; }

    /* =========================================================
       DARK MODE ADAPTATIONS
       ========================================================= */
    .dark .fc .fc-button-primary {
      background: rgba(31,41,55,1) !important;
      border-color: rgba(75,85,99,1) !important;
      color: #d1d5db !important;
    }
    .dark .fc .fc-button-primary:hover {
      background: rgba(55,65,81,1) !important;
      color: #ffffff !important;
    }
    .dark .fc .fc-toolbar-title { color: #f9fafb !important; }
    .dark .fc .fc-scrollgrid { border-color: rgba(55,65,81,0.5) !important; }
    .dark .fc .fc-scrollgrid td, .dark .fc .fc-scrollgrid th { border-color: rgba(55,65,81,0.5) !important; }
    .dark .fc .fc-col-header-cell-cushion { color: #9ca3af !important; }
    .dark .fc .fc-daygrid-day-number { color: #e5e7eb !important; }

    .dark .fc .fc-day-today { background: rgba(201, 162, 74, 0.06) !important; }
    .dark .fc .fc-day-today .fc-daygrid-day-number {
      background: #c9a24a !important; /* Dorado base */
      color: #111827 !important;
    }

    .dark .fc .fc-daygrid-event {
      background: rgba(201, 162, 74, 0.18) !important;
      border-color: rgba(201, 162, 74, 0.35) !important;
      color: rgba(253, 230, 138, 1) !important; /* gold-200 */
    }

    /* =========================================================
       RESPONSIVE TOOLBAR (Móviles)
       ========================================================= */
    @media (max-width: 640px){
      .fc .fc-toolbar{
        flex-direction: column !important;
        gap: 1rem !important;
        align-items: stretch !important;
      }
      .fc .fc-toolbar-chunk{
        display: flex !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
      }
      .fc .fc-toolbar-chunk:nth-child(2) { order: 1 !important; margin-bottom: 0.5rem; }
      .fc .fc-toolbar-chunk:nth-child(1) { order: 2 !important; }
      .fc .fc-toolbar-chunk:nth-child(3) { order: 3 !important; }
    }
  </style>
@endpush

@section('content')
<div class="p-3 sm:p-6 max-w-7xl mx-auto">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-800 flex items-center gap-3 dark:text-gray-100">
        <span class="w-10 h-10 rounded-xl bg-[rgba(201,162,74,0.12)] text-[#c9a24a] dark:bg-[rgba(201,162,74,0.18)] dark:text-[#f6e3b0] flex items-center justify-center shadow-sm">
          <i class="fas fa-calendar-alt text-xl bb-gold"></i>
        </span>
        Calendario de Citas
      </h1>
      <p class="text-sm text-gray-500 mt-1 ml-14 dark:text-gray-400">
        Visualiza, organiza y administra las citas del salón.
      </p>
    </div>

    <div class="flex w-full sm:w-auto">
      <a href="{{ route('admin.citas.create') }}"
         class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm focus:ring-4 focus:ring-[rgba(201,162,74,0.25)]"
         style="background:#c9a24a !important; color:#111827 !important;"
         onmouseover="this.style.filter='brightness(0.98)'"
         onmouseout="this.style.filter='none'"
      >
        <i class="fas fa-plus"></i>
        Nueva cita
      </a>
    </div>
  </div>

  <div class="bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div id="citas-calendar" data-events='@json($calendarEvents ?? [])'></div>
  </div>

</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/es.global.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const calendarEl = document.getElementById('citas-calendar');
      if (!calendarEl) return;

      const events = @json($calendarEvents ?? []);

      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        firstDay: 1, // La semana empieza en Lunes
        initialView: 'dayGridMonth',
        height: 'auto',
        nowIndicator: true,
        selectable: true,
        dayMaxEvents: 4,

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

        dateClick: (info) => {
          const url = new URL(@json(route('admin.citas.create')));
          url.searchParams.set('date', info.dateStr);
          window.location.href = url.toString();
        },

        eventClick: (info) => {
          const template = @json(route('admin.citas.edit', ['cita' => '__ID__']));
          window.location.href = template.replace('__ID__', info.event.id);
        },

        eventMouseEnter(info) {
          const title = info.event.title;
          const start = info.event.start;
          if (!start) return;

          const fecha = start.toLocaleDateString('es-MX', {
            weekday: 'long',
            day: 'numeric',
            month: 'long'
          });

          const hora = start.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
          });

          info.el.title = `Cita: ${title}\nFecha: ${fecha}\nHora: ${hora}`;
        },
      });

      calendar.render();
    });
  </script>
@endpush