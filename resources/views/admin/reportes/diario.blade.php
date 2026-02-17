@php
  $ok = data_get($stats, 'ok', true);
  $msg = data_get($stats, 'mensaje', null);

  $mxn = fn($n) => '$' . number_format((float)$n, 2);

  $inicio = data_get($rango, 'inicio');
  $fin    = data_get($rango, 'fin');

  $ventas = data_get($stats, 'ventas', []);
  $citas  = data_get($stats, 'citas', []);
  $clientesNuevos = (int) data_get($stats, 'clientes.nuevos', 0);

  $metodos = collect(data_get($ventas, 'metodos_pago', []));
  $topServicios = collect(data_get($stats, 'servicios.top', []));
  $topEmpleados = collect(data_get($stats, 'empleados.top', []));
  $ultimasVentas = collect(data_get($ventas, 'ultimas', []));
@endphp

@if(!$ok)
  <div class="p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
    {{ $msg ?? 'No se pudo generar el reporte.' }}
  </div>
@else

  <div class="mb-5 text-sm text-gray-500 dark:text-gray-400">
    Rango: <b>{{ \Carbon\Carbon::parse($inicio)->format('d/m/Y H:i') }}</b> →
    <b>{{ \Carbon\Carbon::parse($fin)->format('d/m/Y H:i') }}</b>
  </div>

  {{-- Cards --}}
  @include('admin.reportes.partials.cards')

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Métodos de pago --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Métodos de pago</h3>

      @if($metodos->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">Sin datos para este día.</p>
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
                  <td class="py-2 text-gray-800 dark:text-gray-200"> {{ $m->metodo_label ?? $m->metodo ?? $m->metodo_pago ?? $m->forma_pago ?? '-' }} </td>
                  <td class="py-2 text-gray-800 dark:text-gray-200"> {{ (int)($m->cantidad ?? 0) }} </td>
                  <td class="py-2 text-gray-800 dark:text-gray-200"> {{ $mxn($m->monto ?? 0) }} </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

   @php
  $ok = data_get($stats, 'ok', true);
  $msg = data_get($stats, 'mensaje', null);

  $mxn = fn($n) => '$' . number_format((float)$n, 2);

  $inicio = data_get($rango, 'inicio');
  $fin    = data_get($rango, 'fin');

  $ventas = data_get($stats, 'ventas', []);
  $citas  = data_get($stats, 'citas', []);
  $clientesNuevos = (int) data_get($stats, 'clientes.nuevos', 0);

  $metodos = collect(data_get($ventas, 'metodos_pago', []));
  $topServicios = collect(data_get($stats, 'servicios.top', []));
  $topEmpleados = collect(data_get($stats, 'empleados.top', []));
  $ultimasVentas = collect(data_get($ventas, 'ultimas', []));
@endphp

    {{-- Top empleados --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top empleados</h3>

      @if($topEmpleados->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">
          Sin datos (requiere que citas tenga empleado_id y ventas se relacione con cita).
        </p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 dark:text-gray-400">
                <th class="py-2">Empleado</th>
                <th class="py-2">Ventas</th>
                <th class="py-2">Ingresos</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topEmpleados as $e)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $e->empleado ?? '(Sin nombre)' }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ (int)($e->ventas ?? 0) }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $mxn($e->ingresos ?? 0) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Últimas ventas --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Últimas ventas</h3>

      @if($ultimasVentas->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">Sin ventas en este rango.</p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 dark:text-gray-400">
                <th class="py-2">Fecha</th>
                <th class="py-2">Total</th>
                <th class="py-2">Pago</th>
                <th class="py-2">Cita</th>
              </tr>
            </thead>
            <tbody>
              @foreach($ultimasVentas as $v)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                  <td class="py-2 text-gray-800 dark:text-gray-200">
                    {{ \Carbon\Carbon::parse($v->fecha_venta ?? $v->created_at ?? now())->format('d/m/Y H:i') }}
                  </td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $mxn($v->total ?? 0) }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $v->forma_pago ?? '-' }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $v->id_cita ?? $v->cita_id ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

  </div>

  {{-- ===========================
     NUEVO BLOQUE: Gráfica + Resumen (debajo de "Últimas ventas")
=========================== --}}
@if(($stats['ok'] ?? false) && isset($stats['ventas']['resumen_chart'], $stats['ventas']['resumen_pagos']))
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Gráfica --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-800">Resumen de ventas</h3>
      <p class="text-sm text-gray-500 mb-4">Ventas por método de pago</p>

      <canvas id="resumenPagosChart"
              data-labels='@json($stats["ventas"]["resumen_chart"]["labels"])'
              data-values='@json($stats["ventas"]["resumen_chart"]["values"])'
              height="120"></canvas>

      {{-- Nota estilo "multipago" --}}
      @php $mixto = (float)($stats['ventas']['resumen_pagos']['mixto']['monto'] ?? 0); @endphp
      @if($mixto > 0)
        <p class="text-xs text-gray-500 mt-3">
          Nota: "Mixto" se suma al total, pero no aparece como barra (igual que multipago).
        </p>
      @endif
    </div>

    {{-- Resumen (lado derecho) --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-800">Resumen</h3>
      <p class="text-sm text-gray-500 mb-4">Desglose por método</p>

      @php
        $rp = $stats['ventas']['resumen_pagos'];
      @endphp

      <div class="space-y-3 text-sm">
        <div class="flex items-center justify-between">
          <span class="text-gray-600">Efectivo</span>
          <span class="font-semibold">${{ number_format($rp['efectivo']['monto'] ?? 0, 2) }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-gray-600">Transferencia</span>
          <span class="font-semibold">${{ number_format($rp['transferencia']['monto'] ?? 0, 2) }}</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-gray-600">Tarjeta</span>
          <span class="font-semibold">${{ number_format($rp['tarjeta']['monto'] ?? 0, 2) }}</span>
        </div>

        @if(($rp['mixto']['monto'] ?? 0) > 0)
          <div class="flex items-center justify-between">
            <span class="text-gray-600">Mixto</span>
            <span class="font-semibold">${{ number_format($rp['mixto']['monto'] ?? 0, 2) }}</span>
          </div>
        @endif

        @if(($rp['otros']['monto'] ?? 0) > 0)
          <div class="flex items-center justify-between">
            <span class="text-gray-600">Otros</span>
            <span class="font-semibold">${{ number_format($rp['otros']['monto'] ?? 0, 2) }}</span>
          </div>
        @endif

        <hr class="my-3">

        <div class="flex items-center justify-between">
          <span class="text-gray-700 font-medium">Total</span>
          <span class="font-bold">${{ number_format($rp['total']['monto'] ?? 0, 2) }}</span>
        </div>
      </div>
    </div>

  </div>
@endif

@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('resumenPagosChart');
  if (!el) return;

  const labels = JSON.parse(el.dataset.labels || '[]');
  const values = JSON.parse(el.dataset.values || '[]');

  new Chart(el, {
    type: 'bar',
    data: { labels, datasets: [{ data: values }] },
    options: { plugins: { legend: { display: false } } }
  });
});
</script>
@endpush
