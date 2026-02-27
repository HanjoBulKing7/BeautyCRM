@extends('layouts.app')

@section('title', 'Reportes - BeautyCRM')

@section('content')
@php
  $tab  = $tab ?? request('tab', 'ventas');
  $tipo = $tipo ?? request('tipo', 'diario');
  $fecha = $fecha ?? request('fecha', now()->toDateString());
@endphp

{{-- CSS Adicional para Flatpickr, rangos y scroll en móviles --}}
<style>
  .flatpickr-day.inRange, 
  .flatpickr-day.inRange:hover,
  .flatpickr-day.selected.startRange, 
  .flatpickr-day.selected.endRange,
  .flatpickr-day.selected.startRange:hover, 
  .flatpickr-day.selected.endRange:hover {
      background: #3b82f6 !important; /* bg-blue-500 */
      border-color: #3b82f6 !important;
      color: #ffffff !important;
  }
  
  /* Ocultar barra de scroll en los tabs para vista móvil */
  .scrollbar-hide::-webkit-scrollbar {
      display: none;
  }
  .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
  }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

{{-- Ajuste Responsive: p-3 en celular, sm:p-6 en pantallas más grandes --}}
<div class="p-3 sm:p-6">

  {{-- Contenedor Principal: p-4 en celular, sm:p-6 en PC --}}
  <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">

    {{-- Cabecera con Título y Calendario --}}
    <h4 class="font-semibold text-lg mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center text-gray-800 dark:text-gray-100">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>
            Reportes de Ventas
        </div>

        {{-- Ajuste Responsive: w-full en móvil para que el input no se desborde --}}
        <div class="flex items-center gap-2 w-full sm:w-auto relative">
            <i class="fas fa-calendar-alt text-gray-400 dark:text-gray-500 absolute left-3 z-10"></i>
            <input
                type="text"
                id="selector_fecha_global"
                class="border rounded-lg pl-9 pr-3 py-2 text-sm text-gray-900 bg-white shadow-sm cursor-pointer focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64 dark:bg-gray-900 dark:border-gray-700 dark:text-white transition-colors"
                placeholder="Seleccionar fecha..."
                readonly
            >
        </div>
    </h4>

    {{-- Navegación de Pestañas (Sub-tabs: Diario / Semanal / Mensual) --}}
    <div class="mb-6 border-b border-gray-100 dark:border-gray-700/50">
      {{-- overflow-x-auto permite deslizar con el dedo si los botones no caben en celular --}}
      <nav class="flex gap-4 sm:gap-6 overflow-x-auto whitespace-nowrap scrollbar-hide pb-2">
        <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'diario','fecha'=>$fecha]) }}"
           class="pb-3 border-b-2 font-medium text-sm transition-colors {{ $tipo === 'diario' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}">
          <i class="fas fa-calendar-day mr-1"></i> Diario
        </a>
        <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'semanal','fecha'=>$fecha]) }}"
           class="pb-3 border-b-2 font-medium text-sm transition-colors {{ $tipo === 'semanal' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}">
          <i class="fas fa-calendar-week mr-1"></i> Semanal
        </a>
        <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'mensual','fecha'=>$fecha]) }}"
           class="pb-3 border-b-2 font-medium text-sm transition-colors {{ $tipo === 'mensual' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}">
          <i class="fas fa-calendar-alt mr-1"></i> Mensual
        </a>
      </nav>
    </div>

    {{-- Render según tipo --}}
    <div class="mt-4">
      @if($tipo === 'diario')
        @include('admin.reportes.diario')
      @elseif($tipo === 'semanal')
        @include('admin.reportes.semanal')
      @else
        @include('admin.reportes.mensual')
      @endif
    </div>

  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tipoFiltro = @json($tipo);
    const fechaActual = @json($fecha);
    const tabActual = @json($tab);
    const inputSelector = document.getElementById('selector_fecha_global');

    function actualizarRuta(nuevaFecha) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabActual);
        url.searchParams.set('tipo', tipoFiltro);
        url.searchParams.set('fecha', nuevaFecha);
        window.location.href = url.toString();
    }

    // Configuración base de Flatpickr inicializada de manera segura
    let fpConfig = {
        locale: flatpickr.l10ns.es,
        defaultDate: fechaActual,
        plugins: [], 
        onChange: function(selectedDates, dateStr, instance) {
            if (!selectedDates.length) return;
            let finalDate = dateStr;

            if (tipoFiltro === 'semanal') {
                const START_DAY = 4; // 4 = Jueves
                const d = new Date(selectedDates[0]);
                const day = d.getDay();
                const diff = (day - START_DAY + 7) % 7;
                d.setDate(d.getDate() - diff);
                
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                finalDate = `${y}-${m}-${dd}`;
            } 
            else if (tipoFiltro === 'mensual') {
                const d = new Date(selectedDates[0]);
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                finalDate = `${y}-${m}-01`;
            }

            actualizarRuta(finalDate);
        }
    };

    if (tipoFiltro === 'diario') {
        fpConfig.dateFormat = "Y-m-d";
        fpConfig.altInput = true;
        fpConfig.altFormat = "d \\de F, Y";

    } else if (tipoFiltro === 'semanal') {
        // Validación para evitar errores si el script de weekSelect carga lento
        if (typeof weekSelectPlugin !== 'undefined') {
            fpConfig.plugins.push(new weekSelectPlugin({ weekNumbers: true }));
        }

        fpConfig.onReady = function(selectedDates, dateStr, instance) {
            // T12:00:00 soluciona el problema de que el navegador quite un día por la zona horaria
            const dStart = selectedDates.length ? new Date(selectedDates[0]) : new Date(fechaActual + 'T12:00:00');
            const START_DAY = 4;
            const diff = (dStart.getDay() - START_DAY + 7) % 7;
            dStart.setDate(dStart.getDate() - diff);

            const dEnd = new Date(dStart);
            dEnd.setDate(dEnd.getDate() + 6);

            const pad = (n) => String(n).padStart(2, '0');
            if(instance.input) {
                instance.input.value = `Semana: ${pad(dStart.getDate())}/${pad(dStart.getMonth()+1)} - ${pad(dEnd.getDate())}/${pad(dEnd.getMonth()+1)}`;
            }
        };

    } else if (tipoFiltro === 'mensual') {
        if (typeof monthSelectPlugin !== 'undefined') {
            fpConfig.plugins.push(new monthSelectPlugin({
                shorthand: true, 
                dateFormat: "Y-m-d", 
                altFormat: "F Y", 
            }));
        }
    }

    if (typeof flatpickr !== 'undefined' && inputSelector) {
        flatpickr(inputSelector, fpConfig);
    }
});
</script>
@endpush
@endsection