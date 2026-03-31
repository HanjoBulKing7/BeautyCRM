@php
  $ultimosClientes = collect(data_get($stats, 'clientes.ultimos', []));
@endphp

<div class="grid grid-cols-1 md:grid-cols-6 gap-4">
  <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Ingresos totales</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ $mxn(data_get($ventas,'monto_total',0)) }}
        </p>
                <p class="text-xs text-gray-400 mt-1">
                    Servicios: {{ $mxn(data_get($ventas,'monto_servicios',0)) }} · Productos: {{ $mxn(data_get($ventas,'monto_productos',0)) }}
                </p>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Reservas totales</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ (int) data_get($citas,'total',0) }}
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Citas completadas</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ (int) data_get($citas,'completadas',0) }}
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
    <p class="text-sm text-gray-500 dark:text-gray-400">Clientes nuevos</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ (int) ($clientesNuevos ?? 0) }}
    </p>

    @if($ultimosClientes->isNotEmpty())
        <div class="mt-2 space-y-1">
        @foreach($ultimosClientes->take(3) as $c)
            @php
            $nombre = trim($c->nombre_completo ?? '') ?: ($c->email ?? 'Cliente');
            $fecha  = isset($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->format('d/m H:i') : '';
            @endphp

            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span class="truncate max-w-[160px]">{{ $nombre }}</span>
            <span class="shrink-0 ml-2">{{ $fecha }}</span>
            </div>
        @endforeach
        </div>
    @endif
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Ventas</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ (int) data_get($ventas,'total_ventas',0) }}
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Ticket promedio</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ $mxn(data_get($ventas,'ticket_promedio',0)) }}
        </p>
  </div>
</div>
