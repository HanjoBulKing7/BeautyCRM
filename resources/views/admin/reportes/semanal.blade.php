@php
  $mxn = fn($n) => '$' . number_format((float)$n, 2);

  $inicio = \Carbon\Carbon::parse(data_get($rango,'inicio'));
  $fin    = \Carbon\Carbon::parse(data_get($rango,'fin'));

  $ventas = data_get($stats, 'ventas', []);
  $citas  = data_get($stats, 'citas', []);
  $clientesNuevos = (int) data_get($stats, 'clientes.nuevos', 0);

  $metodos = collect(data_get($ventas, 'metodos_pago', []));
  $topServicios = collect(data_get($stats, 'servicios.top', []));
  $topEmpleados = collect(data_get($stats, 'empleados.top', []));
@endphp

<div class="mb-5 text-sm text-gray-500 dark:text-gray-400">
  Semana: <b>{{ $inicio->format('d/m/Y') }}</b> → <b>{{ $fin->format('d/m/Y') }}</b>
</div>

<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Ingresos</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($ventas,'monto_total',0)) }}</p>
  </div>
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Ventas</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ (int)data_get($ventas,'total_ventas',0) }}</p>
  </div>
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Ticket Promedio</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($ventas,'ticket_promedio',0)) }}</p>
  </div>
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Citas</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ (int)data_get($citas,'total',0) }}</p>
  </div>
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Clientes nuevos</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $clientesNuevos }}</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Métodos de pago</h3>
    @if($metodos->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">Sin datos.</p>
    @else
      <ul class="space-y-2">
        @foreach($metodos as $m)
          <li class="flex justify-between text-sm">
            <span class="text-gray-700 dark:text-gray-200"> {{ $m->metodo_label ?? $m->metodo ?? '-' }} </span>
            <span class="text-gray-700 dark:text-gray-200">{{ $mxn($m->monto ?? 0) }}</span>
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top servicios</h3>
    @if($topServicios->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">Sin datos.</p>
    @else
      <ul class="space-y-2">
        @foreach($topServicios as $s)
          <li class="flex justify-between text-sm">
            <span class="text-gray-700 dark:text-gray-200">{{ $s->servicio ?? '-' }}</span>
            <span class="text-gray-700 dark:text-gray-200">{{ (int)($s->veces ?? 0) }}</span>
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top empleados</h3>
    @if($topEmpleados->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">Sin datos.</p>
    @else
      <ul class="space-y-2">
        @foreach($topEmpleados as $e)
          <li class="flex justify-between text-sm">
            <span class="text-gray-700 dark:text-gray-200">{{ $e->empleado ?? '(Sin nombre)' }}</span>
            <span class="text-gray-700 dark:text-gray-200">{{ $mxn($e->ingresos ?? 0) }}</span>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>

{{-- ===========================
     NUEVO BLOQUE: Gráfica + Resumen (igual que Diario)
=========================== --}}
@php
  $resumenChart = data_get($ventas, 'resumen_chart', null);
  $resumenPagos = data_get($ventas, 'resumen_pagos', null);
@endphp

@if($resumenChart && $resumenPagos)
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Gráfica --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5 bg-white dark:bg-gray-900">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen de ventas</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ventas por método de pago</p>

      <div style="height:220px">
        <canvas id="resumenPagosChart-semanal"
                data-labels='@json(data_get($resumenChart,"labels",[]))'
                data-values='@json(data_get($resumenChart,"values",[]))'></canvas>
      </div>

      @php $mixto = (float) data_get($resumenPagos,'mixto.monto',0); @endphp
      @if($mixto > 0)
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
          Nota: “Mixto” se suma al total, pero no aparece como barra.
        </p>
      @endif
    </div>

    {{-- Resumen --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5 bg-white dark:bg-gray-900">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Desglose por método</p>

      <div class="space-y-3 text-sm">
        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-300">Efectivo</span>
          <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'efectivo.monto',0)) }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-300">Transferencia</span>
          <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'transferencia.monto',0)) }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-300">Tarjeta</span>
          <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'tarjeta.monto',0)) }}</span>
        </div>

        @if((float)data_get($resumenPagos,'mixto.monto',0) > 0)
          <div class="flex items-center justify-between">
            <span class="text-gray-600 dark:text-gray-300">Mixto</span>
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'mixto.monto',0)) }}</span>
          </div>
        @endif

        @if((float)data_get($resumenPagos,'otros.monto',0) > 0)
          <div class="flex items-center justify-between">
            <span class="text-gray-600 dark:text-gray-300">Otros</span>
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'otros.monto',0)) }}</span>
          </div>
        @endif

        <hr class="my-3 border-gray-200 dark:border-gray-800">

        <div class="flex items-center justify-between">
          <span class="text-gray-700 dark:text-gray-200 font-medium">Total</span>
          <span class="font-bold text-gray-900 dark:text-gray-100">{{ $mxn(data_get($resumenPagos,'total.monto',0)) }}</span>
        </div>
      </div>
    </div>

  </div>

  {{-- Script del chart (semanal) --}}
  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('resumenPagosChart-semanal');
    if (!el || typeof Chart === 'undefined') return;

    const labels = JSON.parse(el.dataset.labels || '[]');
    const values = JSON.parse(el.dataset.values || '[]');

    // evita duplicados
    if (el._chartInstance) el._chartInstance.destroy();

    el._chartInstance = new Chart(el, {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Monto', data: values }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  });
  </script>
  @endpush
@endif
