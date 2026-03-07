@php
    $horaTxt = substr((string)($cita->hora_cita ?? ''), 0, 5);
@endphp

<div class="bb-glass-card p-5 flex flex-col h-full relative">
    
    {{-- Encabezado: ID, Fecha y Estado --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <span class="font-bold text-gray-900 dark:text-gray-100 text-lg">#{{ $cita->id_cita }}</span>
            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }} a las {{ $horaTxt }}
            </div>
        </div>
        {{-- Aquí se usa el color dinámico solo en el texto --}}
        <span class="text-xs font-black uppercase tracking-wider {{ $colorText }}">
            {{ $estadoText }}
        </span>
    </div>

    <hr class="border-gray-100 dark:border-gray-700 mb-4">

    {{-- Info del Cliente y Empleado --}}
    <div class="mb-4 grid grid-cols-2 gap-2">
        <div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Cliente</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $cita->cliente->nombre ?? 'Cliente' }}</p>
            @if($cita->cliente->email ?? false)
                <p class="text-xs text-gray-500 truncate">{{ $cita->cliente->email }}</p>
            @endif
        </div>
        <div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Atiende</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ trim(($cita->empleado->nombre ?? '').' '.($cita->empleado->apellido ?? '')) ?: 'No asignado' }}
            </p>
        </div>
    </div>

    {{-- Servicios --}}
    <div class="mb-4 flex-grow">
        <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Servicios</p>
        <div class="space-y-1.5 bg-gray-50  rounded-lg p-2.5">
            @forelse($cita->servicios as $s)
                <div class="flex justify-between items-center gap-2">
                    <span class="text-xs text-gray-700 dark:text-gray-300 truncate">{{ $s->nombre_servicio }}</span>
                    <span class="text-xs font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                        ${{ number_format($s->pivot->precio_snapshot ?? $s->precio ?? 0, 2) }}
                    </span>
                </div>
            @empty
                <span class="text-xs text-gray-400 italic">Sin servicios</span>
            @endforelse
        </div>
    </div>

    {{-- Total de Venta y Botón (Fijos al fondo) --}}
    <div class="mt-auto">
        <div class="flex items-end justify-between mb-4">
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Total Venta</p>
                @if($cita->venta)
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold bb-gold">${{ number_format($cita->venta->total, 2) }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded  text-gray-600 dark:text-gray-300 font-semibold uppercase">
                            {{ $cita->venta->forma_pago ?? 'Efectivo' }}
                        </span>
                    </div>
                @else
                    <span class="text-sm text-gray-400 italic">Sin venta registrada</span>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.citas.show', $cita->id_cita) }}" class="bb-action-ink">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>Ver Detalles</span>
        </a>
    </div>

</div>