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
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $m->metodo ?? $m->metodo_pago ?? $m->forma_pago ?? '-' }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ (int)($m->cantidad ?? 0) }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $mxn($m->monto ?? 0) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Top servicios --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top servicios</h3>

      @if($topServicios->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">
          Sin datos (si tu pivot cita_servicio no existe o no está ligado a citas, aquí quedará vacío).
        </p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 dark:text-gray-400">
                <th class="py-2">Servicio</th>
                <th class="py-2">Veces</th>
                <th class="py-2">Ingresos*</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topServicios as $s)
                <tr class="border-t border-gray-100 dark:border-gray-800">
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ $s->servicio ?? '-' }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">{{ (int)($s->veces ?? 0) }}</td>
                  <td class="py-2 text-gray-800 dark:text-gray-200">
                    {{ isset($s->ingresos_estimados) ? $mxn($s->ingresos_estimados) : '—' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
          * “Ingresos” solo aparece si tu tabla pivot guarda precio (columna precio / precio_servicio).
        </p>
      @endif
    </div>

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
@endif