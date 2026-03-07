@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    {{-- ✅ Estilos locales usando el dorado institucional y CSS para Flatpickr --}}
    <style>
        :root{
            --bb-gold: rgba(201,162,74,.95);
            --bb-gold-soft: rgba(201,162,74,.14);
            --bb-gold-border: rgba(201,162,74,.22);
            --bb-glass: rgba(255,255,255,.90);
        }

        .bb-glass-card{
            background: var(--bb-glass);
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border: 1px solid rgba(17,24,39,.08);
            box-shadow: 0 4px 15px rgba(0,0,0,.03);
            border-radius: 1rem;
        }

        .bb-icon-pill{
            width: 42px; height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.80);
            border: 1px solid rgba(201,162,74,.25);
            box-shadow: 0 4px 10px rgba(17,24,39,.04);
            flex: 0 0 auto;
        }

        .bb-icon-pill svg{
            display: block;
        }

        .bb-gold{ color: var(--bb-gold) !important; }

        .bb-btn-ghost{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:.5rem;
            padding:.6rem 1rem;
            border-radius: .75rem;
            font-weight: 600;
            color: rgba(17,24,39,.88);
            background: #ffffff;
            border: 1px solid rgba(17,24,39,.15);
            box-shadow: 0 2px 6px rgba(17,24,39,.04);
            transition: all .2s ease;
        }
        .bb-btn-ghost:hover{
            transform: translateY(-1px);
            background: #f9fafb;
            box-shadow: 0 4px 10px rgba(17,24,39,.08);
        }

        .bb-input{
            width: 100%;
            border-radius: .75rem;
            border: 1px solid rgba(17,24,39,.15);
            background: #ffffff;
            color: #111827;
            padding: .65rem 1rem .65rem 2.5rem;
            outline: none;
            transition: all .2s ease;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .bb-input:focus{
            border-color: rgba(201,162,74,.60);
            box-shadow: 0 0 0 3px rgba(201,162,74,.15);
        }

        .flatpickr-input[readonly]{
            cursor: pointer;
        }

        .bb-pill-gold{
            display:inline-flex;
            align-items:center;
            padding: .25rem .65rem;
            border-radius: .5rem;
            background: var(--bb-gold-soft);
            border: 1px solid var(--bb-gold-border);
            color: rgba(17,24,39,.90);
            font-weight: 700;
        }

        .bb-action-ink{
            display:inline-flex;
            align-items:center;
            gap:.3rem;
            padding: .5rem .75rem;
            border-radius: .5rem;
            background: #f3f4f6;
            color: rgba(17,24,39,.85);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all .2s ease;
            width: 100%;
            justify-content: center;
        }
        .bb-action-ink:hover{ background: #e5e7eb; color: #000; transform: translateY(-1px); }

        /* ✅ KPI strip (una sola fila) */
        .bb-kpi-strip{
            display:grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
        @media (max-width: 640px){
            .bb-kpi-strip{ grid-template-columns: 1fr; }
        }
        .bb-kpi{
            padding: 14px 16px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 14px;
        }
        .bb-kpi__left{
            display:flex;
            align-items:center;
            gap: 12px;
            min-width: 0;
        }
        .bb-kpi__emoji{
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background: rgba(249,250,251,.90);
            border: 1px solid rgba(17,24,39,.10);
            box-shadow: 0 4px 10px rgba(17,24,39,.04);
            flex: 0 0 auto;
        }
        .dark .bb-kpi__emoji{
            background: rgba(17,24,39,.55);
            border-color: rgba(255,255,255,.10);
        }
        .bb-kpi__label{
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(107,114,128,1);
        }
        .dark .bb-kpi__label{ color: rgba(156,163,175,.95); }

        .bb-kpi__value{
            font-size: 26px;
            font-weight: 900;
            line-height: 1;
            color: rgba(17,24,39,.92);
            white-space: nowrap;
        }
        .dark .bb-kpi__value{ color: rgba(249,250,251,.95); }

        .bb-kpi__value.bb-kpi__gold{ color: rgba(201,162,74,.98); }
        .dark .bb-kpi__value.bb-kpi__gold{ color: rgba(246,227,176,.98); }

        /* ✅ Modo Oscuro Adaptado */
        .dark .bb-glass-card{
            background: rgba(31,41,55,.60);
            border-color: rgba(255,255,255,.10);
        }
        .dark .bb-icon-pill{
            background: rgba(17,24,39,.50);
            border-color: rgba(201,162,74,.40);
        }
        .dark .bb-input{
            background: rgba(17,24,39,.50);
            border-color: rgba(255,255,255,.15);
            color: #f9fafb;
        }
        .dark .bb-input:focus{ box-shadow: 0 0 0 3px rgba(201,162,74,.25); }

        .dark .bb-btn-ghost{
            background: rgba(31,41,55,.80);
            border-color: rgba(255,255,255,.15);
            color: #f9fafb;
        }
        .dark .bb-btn-ghost:hover{ background: rgba(55,65,81,.90); }
        .dark .bb-action-ink{ background: rgba(55,65,81,.80); color: #f9fafb; }
        .dark .bb-action-ink:hover{ background: rgba(75,85,99,.90); }

        .dark .text-gray-900, .dark .text-gray-800 { color: rgba(249,250,251,.95) !important; }
        .dark .text-gray-700 { color: rgba(229,231,235,.92) !important; }
        .dark .text-gray-600 { color: rgba(209,213,219,.86) !important; }
        .dark .text-gray-500 { color: rgba(156,163,175,.92) !important; }

        /* ✅ CSS para el calendario Flatpickr (Dorado) */
        .flatpickr-day.inRange,
        .flatpickr-day.inRange:hover,
        .flatpickr-day.selected.startRange,
        .flatpickr-day.selected.endRange,
        .flatpickr-day.selected.startRange:hover,
        .flatpickr-day.selected.endRange:hover {
            background: #c9a24a !important;
            border-color: #c9a24a !important;
            color: #ffffff !important;
        }
        
        /* ✅ Alturas iguales para las cards */
        .card-citas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- ✅ Lógica y Agrupación en Blade --}}
    @php
        // Ordenamiento original
        if (method_exists($citasCompletadas, 'setCollection') && method_exists($citasCompletadas, 'getCollection')) {
            $sorted = $citasCompletadas->getCollection()->sortBy(function($c) {
                $f = $c->fecha_cita ? \Carbon\Carbon::parse($c->fecha_cita)->format('Y-m-d') : '9999-12-31';
                $h = substr((string)($c->hora_cita ?? ''), 0, 5);
                return $f.' '.$h;
            })->values();
            $citasCompletadas->setCollection($sorted);
        }

        // Agrupando la colección actual para separarlos por estados
        $citasArray = method_exists($citasCompletadas, 'getCollection') ? $citasCompletadas->getCollection() : $citasCompletadas;
        
        $citasPorEstado = collect($citasArray)->groupBy(function($cita) {
            return strtolower(trim((string)($cita->estado_cita ?? '')));
        });

        $confirmadas = $citasPorEstado->get('confirmada', collect([]));
        $completadas = $citasPorEstado->get('completada', collect([]));
        $canceladas = $citasPorEstado->get('cancelada', collect([]));

        $pendientesCountView = $pendientesCount ?? $confirmadas->count();
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3 dark:text-gray-100">
            <span class="bb-icon-pill">
                <svg class="w-6 h-6 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </span>
            Ventas (Citas)
        </h1>
    </div>

    {{-- Filtro --}}
    <div class="bb-glass-card p-4 sm:p-5 mb-6">
        <form id="filtroVentasForm" method="GET" action="{{ route('admin.ventas.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full relative">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Rango de Fechas</label>
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <input type="text" id="rango_fechas" class="bb-input w-full" placeholder="Seleccionar inicio y fin" readonly>
                    <input type="hidden" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio ?? '' }}">
                    <input type="hidden" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin ?? '' }}">
                </div>
            </div>
            <div class="w-full sm:w-auto mt-2 sm:mt-0">
                <a href="{{ route('admin.ventas.index') }}" class="bb-btn-ghost w-full justify-center sm:w-auto h-[42px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="bb-kpi-strip mb-8">
        <div class="bb-glass-card bb-kpi">
            <div class="bb-kpi__left">
                <div class="bb-kpi__emoji">
                    <svg class="w-6 h-6 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="bb-kpi__label">Total Ventas</div>
                    <div class="bb-kpi__value bb-kpi__gold">
                        ${{ number_format($totalVentas ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="bb-glass-card bb-kpi">
            <div class="bb-kpi__left">
                <div class="bb-kpi__emoji">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="bb-kpi__label">Completadas</div>
                    <div class="bb-kpi__value">
                        {{ $ventasCount ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="bb-glass-card bb-kpi">
            <div class="bb-kpi__left">
                <div class="bb-kpi__emoji">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="bb-kpi__label">Pendientes</div>
                    <div class="bb-kpi__value">
                        {{ $pendientesCountView ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ CONTENEDOR DE CARDS POR ESTADO --}}
    
    @if(count($citasArray) === 0)
        <div class="bb-glass-card p-12 text-center">
            <div class="mx-auto flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3 mt-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <p class="font-semibold text-gray-800 dark:text-gray-100 text-base">No hay citas registradas</p>
            <p class="text-sm mt-1 text-gray-500">Ajusta el rango de fechas para ver más resultados.</p>
        </div>
    @else

        {{-- SECCIÓN: CONFIRMADAS --}}
        @if($confirmadas->count() > 0)
        <div class="mb-10">
            <h2 class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                Citas Confirmadas ({{ $confirmadas->count() }})
            </h2>
            <div class="card-citas-grid">
                @foreach($confirmadas as $cita)
                    @include('components.cita-card', ['cita' => $cita, 'colorText' => 'text-yellow-600 dark:text-yellow-400', 'estadoText' => 'Confirmada'])
                @endforeach
            </div>
        </div>
        @endif

        {{-- SECCIÓN: COMPLETADAS --}}
        @if($completadas->count() > 0)
        <div class="mb-10">
            <h2 class="text-lg font-bold text-green-600 dark:text-green-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Citas Completadas ({{ $completadas->count() }})
            </h2>
            <div class="card-citas-grid">
                @foreach($completadas as $cita)
                    @include('components.cita-card', ['cita' => $cita, 'colorText' => 'text-green-600 dark:text-green-400', 'estadoText' => 'Completada'])
                @endforeach
            </div>
        </div>
        @endif

        {{-- SECCIÓN: CANCELADAS --}}
        @if($canceladas->count() > 0)
        <div class="mb-10">
            <h2 class="text-lg font-bold text-red-500 dark:text-red-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                Citas Canceladas ({{ $canceladas->count() }})
            </h2>
            <div class="card-citas-grid">
                @foreach($canceladas as $cita)
                    @include('components.cita-card', ['cita' => $cita, 'colorText' => 'text-red-500 dark:text-red-400', 'estadoText' => 'Cancelada'])
                @endforeach
            </div>
        </div>
        @endif

    @endif

    {{-- Paginación --}}
    @if(method_exists($citasCompletadas, 'hasPages') && $citasCompletadas->hasPages())
        <div class="mt-8">
            {{ $citasCompletadas->links() }}
        </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filtroVentasForm');
    const inputRango = document.getElementById('rango_fechas');
    const inputInicio = document.getElementById('fecha_inicio');
    const inputFin = document.getElementById('fecha_fin');

    flatpickr(inputRango, {
        mode: "range",
        locale: flatpickr.l10ns.es,
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M, Y",
        defaultDate: [inputInicio.value, inputFin.value].filter(Boolean),
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                const pad = n => n < 10 ? '0' + n : n;
                const d1 = selectedDates[0];
                const d2 = selectedDates[1];

                inputInicio.value = `${d1.getFullYear()}-${pad(d1.getMonth()+1)}-${pad(d1.getDate())}`;
                inputFin.value = `${d2.getFullYear()}-${pad(d2.getMonth()+1)}-${pad(d2.getDate())}`;

                form.submit();
            }
        }
    });
});
</script>
@endpush
@endsection
