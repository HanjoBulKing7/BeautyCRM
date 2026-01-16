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
            <span class="text-gray-700 dark:text-gray-200">{{ $m->metodo ?? '-' }}</span>
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
