@extends('layouts.app')

@section('title', 'Citas')
@section('page-title', 'Citas')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

  <style>
    /* =========================
       FullCalendar: evitar botones encimados + spacing responsive
       (igual que tu ejemplo)
       ========================= */

    .fc .fc-button-group{
      display:inline-flex !important;
      gap:.5rem !important;
    }
    .fc .fc-button-group > .fc-button{
      margin:0 !important;
    }

    .fc .fc-header-toolbar{
      flex-wrap:wrap !important;
      gap:.75rem !important;
    }
    .fc .fc-toolbar-chunk{
      display:flex !important;
      align-items:center !important;
      flex-wrap:wrap !important;
      gap:.5rem !important;
    }

    @media (max-width:640px){
      .fc .fc-header-toolbar{
        flex-direction:column !important;
        align-items:stretch !important;
      }
      .fc .fc-toolbar-chunk{
        width:100% !important;
        justify-content:flex-start !important;
      }

      .fc .fc-toolbar-chunk:nth-child(2){ order:1 !important; }
      .fc .fc-toolbar-chunk:nth-child(1){ order:2 !important; }
      .fc .fc-toolbar-chunk:nth-child(3){ order:3 !important; }

      .fc .fc-button{
        padding:.32rem .55rem !important;
        font-size:.75rem !important;
        border-radius:.85rem !important;
      }
      .fc .fc-toolbar-title{
        font-size:1.05rem !important;
      }
      .fc .fc-button-group{ gap:.4rem !important; }
    }

    @media (min-width:768px){
      .fc .fc-header-toolbar{ gap:1rem !important; }
      .fc .fc-toolbar-chunk{ gap:.6rem !important; }
      .fc .fc-button-group{ gap:.55rem !important; }
    }

    /* =========================
       FullCalendar: Glass + tu dorado
       ========================= */

    /* Botones */
    .fc .fc-button{
      border-radius: .95rem !important;
      border: 1px solid rgba(201,162,74,.22) !important;
      background: rgba(255,255,255,.65) !important;
      color: rgba(17,24,39,.88) !important;
      box-shadow: 0 10px 22px rgba(17,24,39,.07) !important;
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease !important;
    }
    .fc .fc-button:hover{
      transform: translateY(-1px);
      background: rgba(255,255,255,.80) !important;
      box-shadow: 0 16px 30px rgba(17,24,39,.09) !important;
    }
    .fc .fc-button:focus{
      box-shadow: 0 0 0 3px rgba(201,162,74,.22) !important;
      outline: none !important;
    }

    /* Botón activo */
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active{
      background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95)) !important;
      color:#111827 !important;
      border-color: rgba(201,162,74,.35) !important;
      box-shadow: 0 12px 28px rgba(201,162,74,.18) !important;
    }

    /* Título */
    .fc .fc-toolbar-title{
      font-weight: 800 !important;
      color: rgba(17,24,39,.88) !important;
      letter-spacing: .2px;
    }

    /* Grid */
    .fc .fc-scrollgrid,
    .fc .fc-scrollgrid table{
      border-color: rgba(17,24,39,.08) !important;
    }
    .fc .fc-col-header-cell-cushion{
      color: rgba(17,24,39,.70) !important;
      font-weight: 700 !important;
      text-decoration:none !important;
    }
    .fc .fc-daygrid-day-number{
      color: rgba(17,24,39,.70) !important;
      text-decoration:none !important;
      font-weight: 600 !important;
    }

    /* Eventos (marca/etiqueta) */
    .fc .fc-daygrid-event{
      border-radius: .8rem !important;
      border: 1px solid rgba(201,162,74,.20) !important;
      background: rgba(201,162,74,.12) !important;
      color: rgba(17,24,39,.88) !important;
      padding: .15rem .35rem !important;
    }

    /* “Hoy” */
    .fc .fc-day-today{
      background: rgba(201,162,74,.10) !important;
    }

    /* =========================
       Dark mode (tu body usa .dark-mode)
       ========================= */
    .dark-mode .fc .fc-button{
      background: rgba(17,24,39,.35) !important;
      border-color: rgba(255,255,255,.10) !important;
      color: rgba(249,250,251,.92) !important;
      box-shadow: 0 10px 22px rgba(0,0,0,.25) !important;
    }
    .dark-mode .fc .fc-button:hover{
      background: rgba(17,24,39,.55) !important;
    }
    .dark-mode .fc .fc-toolbar-title{
      color: rgba(249,250,251,.95) !important;
    }
    .dark-mode .fc .fc-scrollgrid,
    .dark-mode .fc .fc-scrollgrid table{
      border-color: rgba(255,255,255,.10) !important;
    }
    .dark-mode .fc .fc-col-header-cell-cushion,
    .dark-mode .fc .fc-daygrid-day-number{
      color: rgba(229,231,235,.85) !important;
    }
    .dark-mode .fc .fc-daygrid-event{
      background: rgba(201,162,74,.14) !important;
      border-color: rgba(201,162,74,.22) !important;
      color: rgba(249,250,251,.92) !important;
    }
    .dark-mode .fc .fc-day-today{
      background: rgba(201,162,74,.08) !important;
    }
  </style>
@endpush


@section('content')
<div class="container mx-auto px-4">

  <!-- Card contenedor (glass) -->
  <div class="bb-glass-card p-4 md:p-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <i class="fas fa-calendar-days" style="color: rgba(201,162,74,.95)"></i>
          Calendario de citas
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Visualiza y administra las citas del salón
        </p>
      </div>
  
      <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full md:w-auto">
        {{-- (Opcional) Aquí puedes meter filtros en el futuro (empleado/estado/servicio) --}}

        <a href="{{ route('admin.citas.create') }}"
           class="bb-btn-gold w-full sm:w-auto inline-flex items-center justify-center gap-2">
          <i class="fas fa-plus"></i>
          Nueva cita
        </a>
      </div>
    </div>

    <!-- Card calendario -->
    <div class="bb-glass-card overflow-hidden">
      <div class="p-2 md:p-3">
        <div id="citas-calendar" data-events='@json($calendarEvents ?? [])'></div>

      </div>
    </div>

    {{-- Lista/tabla debajo (opcional): si quieres conservar tu tabla, la dejamos separada --}}
    {{-- Si no la quieres, me dices y la quitamos. --}}

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

      // ✅ Mantiene tu implementación actual: eventos ya calculados desde el controller
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

        // ✅ click en día: abre create con date preseleccionada
        dateClick: (info) => {
          const url = new URL(@json(route('admin.citas.create')));
          url.searchParams.set('date', info.dateStr);
          window.location.href = url.toString();
        },

        // ✅ click en evento: manda a editar (o show si prefieres)
        eventClick: (info) => {
          const template = @json(route('admin.citas.edit', ['cita' => '__ID__']));
          window.location.href = template.replace('__ID__', info.event.id);
        },


        // Tooltip simple
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
