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
  Mes: <b>{{ $inicio->translatedFormat('F Y') }}</b>
  ({{ $inicio->format('d/m') }} → {{ $fin->format('d/m') }})
</div>

  {{-- Cards --}}
  @include('admin.reportes.partials.cards')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Métodos de pago</h3>
    @if($metodos->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">Sin datos.</p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 dark:text-gray-400">
              <th class="py-2">Método</th>
              <th class="py-2">Ventas</th>
              <th class="py-2">Monto</th>
            </tr>
          </thead>
          <tbody>
            @foreach($metodos as $m)
              <tr class="border-t border-gray-100 dark:border-gray-800">
                <td class="py-2 text-gray-800 dark:text-gray-200">{{ $m->metodo_label ?? $m->metodo ?? '-' }}</td>
                <td class="py-2 text-gray-800 dark:text-gray-200">{{ (int)($m->cantidad ?? 0) }}</td>
                <td class="py-2 text-gray-800 dark:text-gray-200">{{ $mxn($m->monto ?? 0) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top servicios & empleados</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Servicios</p>
        @if($topServicios->isEmpty())
          <p class="text-gray-500 dark:text-gray-400 text-sm">Sin datos.</p>
        @else
          <ul class="space-y-2 text-sm">
            @foreach($topServicios as $s)
              <li class="flex justify-between">
                <span class="text-gray-700 dark:text-gray-200">{{ $s->servicio ?? '-' }}</span>
                <span class="text-gray-700 dark:text-gray-200">{{ (int)($s->veces ?? 0) }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>

      <div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Empleados</p>
        @if($topEmpleados->isEmpty())
          <p class="text-gray-500 dark:text-gray-400 text-sm">Sin datos.</p>
        @else
          <ul class="space-y-2 text-sm">
            @foreach($topEmpleados as $e)
              <li class="flex justify-between">
                <span class="text-gray-700 dark:text-gray-200">{{ $e->empleado ?? '(Sin nombre)' }}</span>
                <span class="text-gray-700 dark:text-gray-200">{{ $mxn($e->ingresos ?? 0) }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
{{-- Métodos de pago --}}
<div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Métodos de pago</h3>

  @if($metodos->isEmpty())
    <p class="text-gray-500 dark:text-gray-400">Sin datos.</p>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-gray-500 dark:text-gray-400">
            <th class="py-2">Método</th>
            <th class="py-2">Ventas</th>
            <th class="py-2">Monto</th>
          </tr>
        </thead>
        <tbody>
          @foreach($metodos as $m)
            <tr class="border-t border-gray-100 dark:border-gray-800">
              <td class="py-2 text-gray-800 dark:text-gray-200">
                {{ $m->metodo_label ?? $m->metodo ?? $m->metodo_pago ?? $m->forma_pago ?? '-' }}
              </td>
              <td class="py-2 text-gray-800 dark:text-gray-200">
                {{ (int)($m->cantidad ?? 0) }}
              </td>
              <td class="py-2 text-gray-800 dark:text-gray-200">
                {{ $mxn($m->monto ?? 0) }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
{{-- ===========================
     Resumen de ventas (Mensual)
     =========================== --}}
@php
  $chart = data_get($ventas, 'resumen_chart', ['labels'=>[], 'values'=>[]]);
  $rp    = data_get($ventas, 'resumen_pagos', []);

  $efectivo = (float) data_get($rp, 'efectivo.monto', 0);
  $transfer = (float) data_get($rp, 'transferencia.monto', 0);
  $tarjeta  = (float) data_get($rp, 'tarjeta.monto', 0);
  $total    = (float) data_get($rp, 'total.monto', 0);

  $chartId = 'chartResumenMensual';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
  {{-- Card: Gráfica --}}
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen de ventas</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ventas por método de pago</p>

    <div class="relative" style="height: 240px;">
      <canvas id="{{ $chartId }}"></canvas>
    </div>
  </div>

  {{-- Card: Resumen --}}
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Desglose por método</p>

    <div class="space-y-3 text-sm">
      <div class="flex justify-between">
        <span class="text-gray-700 dark:text-gray-200">Efectivo</span>
        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $mxn($efectivo) }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-700 dark:text-gray-200">Transferencia</span>
        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $mxn($transfer) }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-700 dark:text-gray-200">Tarjeta</span>
        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $mxn($tarjeta) }}</span>
      </div>

      <div class="border-t border-gray-200 dark:border-gray-800 pt-3 mt-3 flex justify-between">
        <span class="text-gray-700 dark:text-gray-200 font-semibold">Total</span>
        <span class="text-gray-900 dark:text-gray-100 font-bold">{{ $mxn($total) }}</span>
      </div>
    </div>
  </div>
</div>

{{-- Chart.js (si no está cargado globalmente) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  // labels/values desde PHP
  const labels = @json(data_get($chart, 'labels', []));
  const values = @json(data_get($chart, 'values', []));

  const canvas = document.getElementById(@json($chartId));
  if (!canvas) return;

  // Si no hay datos, no dibujar
  if (!labels.length || !values.length) return;

  // Evitar doble render si navegas/recargas con Turbo/etc
  if (canvas.dataset.chartReady === "1") return;
  canvas.dataset.chartReady = "1";

  // Si Chart no existe, te falta incluir chart.js
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js no está cargado. Asegúrate de incluirlo en el layout o en la vista.');
    return;
  }

  new Chart(canvas, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Ventas ($)',
        data: values,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});
</script>

