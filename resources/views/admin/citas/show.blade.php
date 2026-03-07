{{-- resources/views/admin/citas/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    // Colección multi-servicio
    $servicios = $cita->servicios ?? collect();
    $servicioLegacy = $cita->servicio ?? null;
    $tieneServicios = $servicios->count() > 0;

    // Totales
    $totalServicios = 0;
    $duracionTotal = 0;

    if ($tieneServicios) {
        $totalServicios = $servicios->sum(function ($s) {
            return (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0);
        });
        $duracionTotal = $servicios->sum(function ($s) {
            return (int) ($s->pivot->duracion_snapshot ?? $s->duracion ?? 0);
        });
    } elseif ($servicioLegacy) {
        $totalServicios = (float) ($servicioLegacy->precio ?? 0);
        $duracionTotal = (int) ($servicioLegacy->duracion ?? 60);
    }

    // Colores basados en el estado
    $estado = strtolower(trim($cita->estado_cita));
    $estadoColorText = 'text-gray-600';
    $estadoColorBg = 'bg-gray-100';
    $estadoBorder = 'border-gray-200';

    if ($estado === 'completada') {
        $estadoColorText = 'text-green-700';
        $estadoColorBg = 'bg-green-50';
        $estadoBorder = 'border-green-200';
    } elseif ($estado === 'confirmada') {
        $estadoColorText = 'text-yellow-700';
        $estadoColorBg = 'bg-yellow-50';
        $estadoBorder = 'border-yellow-200';
    } elseif ($estado === 'cancelada') {
        $estadoColorText = 'text-red-700';
        $estadoColorBg = 'bg-red-50';
        $estadoBorder = 'border-red-200';
    } elseif ($estado === 'pendiente') {
        $estadoColorText = 'text-blue-700';
        $estadoColorBg = 'bg-blue-50';
        $estadoBorder = 'border-blue-200';
    }
@endphp

<div class="container mx-auto px-4 py-6 max-w-5xl">

    <style>
        :root{
            --bb-gold: rgba(201,162,74,1);
        }
        .bb-gold{ color: var(--bb-gold) !important; }

        .bb-glass-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
        }

        .bb-icon-pill {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid rgba(201,162,74,.3);
            box-shadow: 0 2px 4px rgba(0,0,0,.02);
            flex: 0 0 auto;
        }

        .bb-btn-ghost {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:.5rem;
            padding:.6rem 1.2rem;
            border-radius: .75rem;
            font-weight: 600;
            color: #374151;
            background: #ffffff;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
            transition: all .2s;
        }
        .bb-btn-ghost:hover {
            background: #f3f4f6;
        }
    </style>

    {{-- Encabezado --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="bb-icon-pill">
                <svg class="w-7 h-7 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    Cita <span class="bb-gold">#{{ $cita->id_cita }}</span>
                    <span class="text-[10px] uppercase font-black tracking-wider px-2.5 py-1 rounded-full border {{ $estadoColorText }} {{ $estadoColorBg }} {{ $estadoBorder }}">
                        {{ $cita->estado_cita }}
                    </span>
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">Detalles completos de la reservación</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.citas.edit', $cita->id_cita) }}" class="bb-btn-ghost text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Editar
            </a>
            <a href="{{ route('admin.ventas.index') }}" class="bb-btn-ghost text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Indicadores Rápidos --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bb-glass-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500">Total Servicios</p>
                <p class="text-lg font-black text-gray-900">${{ number_format($totalServicios, 2) }}</p>
            </div>
        </div>

        <div class="bb-glass-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500">Duración</p>
                <p class="text-lg font-bold text-gray-900">{{ $duracionTotal > 0 ? $duracionTotal : 0 }} min</p>
            </div>
        </div>

        <div class="bb-glass-card p-4 flex items-center gap-4 col-span-2 md:col-span-2">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200">
                @if($cita->synced_with_google)
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                @else
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                @endif
            </div>
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-500">Google Calendar</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $cita->synced_with_google ? 'Sincronizada' : 'No sincronizada' }}
                    </p>
                    @if($cita->google_event_id)
                        <a href="https://calendar.google.com/calendar/event?eid={{ $cita->google_event_id }}" target="_blank" class="text-[10px] font-semibold text-blue-600 hover:underline">
                            (Ver evento)
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="bb-glass-card p-6 sm:p-8 mb-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            {{-- Columna Izquierda --}}
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Agendado Para
                </h3>
                <div class="space-y-4 bg-white p-5 rounded-xl border border-gray-200">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Fecha</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Hora</span>
                        <span class="font-semibold text-gray-900">{{ substr($cita->hora_cita, 0, 5) }}</span>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha --}}
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Participantes
                </h3>
                <div class=" bg-white p-5 rounded-xl border border-gray-200 h-full">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
                        <div>
                            <span class="block text-[10px] uppercase text-gray-400 font-bold mb-0.5">Cliente</span>
                            <span class="font-semibold text-gray-900">{{ optional($cita->cliente)->name ?? optional($cita->cliente)->nombre ?? 'N/A' }}</span>
                        </div>
                        @if(optional($cita->cliente)->email)
                            <span class="text-xs text-gray-500">{{ $cita->cliente->email }}</span>
                        @endif
                    </div>
                    
                    <div>
                        <span class="block text-[10px] uppercase text-gray-400 font-bold mb-0.5">Atiende (Empleado)</span>
                        <span class="font-medium text-gray-800">{{ optional($cita->empleado)->name ?? optional($cita->empleado)->nombre ?? 'No asignado' }}</span>
                    </div>
                </div>
            </div>
        </div>


        {{-- Sección de Servicios (CORREGIDA) --}}
        <div class="mb-8 mt-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                Servicios Contratados @if($tieneServicios) <span class="text-gray-400 font-normal ml-1">({{ $servicios->count() }})</span> @endif
            </h3>

            @if($tieneServicios)
                <div class="space-y-3">
                    @foreach($servicios as $s)
                        @php
                            $precio = (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0);
                            $dur = (int) ($s->pivot->duracion_snapshot ?? $s->duracion ?? 0);
                        @endphp
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-gray-50 p-4 rounded-xl border border-gray-200 gap-4">
                            <div>
                                <p class="font-bold text-gray-900">{{ $s->nombre_servicio }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $s->descripcion ?? 'Sin descripción adicional' }}</p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center w-full sm:w-auto">
                                <p class="text-lg font-black text-gray-900">${{ number_format($precio, 2) }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ $dur > 0 ? $dur : 0 }} mins</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($servicioLegacy)
                <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-gray-50 p-4 rounded-xl border border-gray-200 gap-4">
                    <div>
                        <p class="font-bold text-gray-900">{{ $servicioLegacy->nombre_servicio }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $servicioLegacy->descripcion ?? 'Sin descripción adicional' }}</p>
                    </div>
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center w-full sm:w-auto">
                        <p class="text-lg font-black text-gray-900">${{ number_format($servicioLegacy->precio ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $servicioLegacy->duracion ?? 60 }} mins</p>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-xl border border-dashed border-gray-300 text-center text-gray-500 text-sm">
                    No hay servicios asignados a esta cita.
                </div>
            @endif
        </div>

        {{-- Observaciones --}}
        @if($cita->observaciones)
            <hr class="border-gray-200 my-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Notas y Observaciones
                </h3>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-700 italic">"{{ $cita->observaciones }}"</p>
                </div>
            </div>
        @endif
        
        {{-- Venta Generada --}}
        @if($estado === 'completada' && $cita->venta)
            <hr class="border-gray-200 my-8">
            <div class="bg-green-50 border border-green-200 p-5 rounded-xl flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Venta Registrada #{{ $cita->venta->id_venta }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Total: <span class="font-semibold text-green-700">${{ number_format($cita->venta->total, 2) }}</span> • Pago en {{ ucfirst($cita->venta->forma_pago) }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.ventas.show', $cita->venta->id_venta) }}" class="text-xs font-bold uppercase tracking-wider bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition w-full sm:w-auto text-center text-gray-800">
                    Ver Ticket
                </a>
            </div>
        @endif

    </div>

    {{-- Acciones Finales (CORREGIDAS) --}}
    <div class="flex flex-col sm:flex-row justify-end gap-4">
        @if($estado != 'cancelada' && $estado != 'completada')
            <form action="{{ route('admin.citas.update', $cita->id_cita) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="estado_cita" value="cancelada">
                <input type="hidden" name="cliente_id" value="{{ $cita->cliente_id }}">
                <input type="hidden" name="fecha_cita" value="{{ $cita->fecha_cita }}">
                <input type="hidden" name="hora_cita" value="{{ $cita->hora_cita }}">
                <input type="hidden" name="descuento" value="{{ $cita->descuento ?? 0 }}">
                <input type="hidden" name="observaciones" value="{{ $cita->observaciones }}">
                @foreach($cita->servicios as $srv)
                    <input type="hidden" name="servicios[{{ $loop->index }}][id_servicio]" value="{{ $srv->id_servicio }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][precio_snapshot]" value="{{ $srv->pivot->precio_snapshot ?? $srv->precio }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][duracion_snapshot]" value="{{ $srv->pivot->duracion_snapshot ?? $srv->duracion_minutos }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][id_empleado]" value="{{ $srv->pivot->id_empleado ?? $cita->empleado_id }}">
                @endforeach
                <button type="submit" onclick="" class="w-full sm:w-auto px-6 py-3 rounded-xl border-2 border-red-200 text-red-600 bg-white hover:bg-red-50 font-bold text-sm transition text-center flex justify-center items-center">
                    Cancelar Cita
                </button>
            </form>
        @endif

        @if($estado == 'pendiente' || $estado == 'confirmada')
            <form action="{{ route('admin.citas.update', $cita->id_cita) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="estado_cita" value="completada">
                <input type="hidden" name="cliente_id" value="{{ $cita->cliente_id }}">
                <input type="hidden" name="fecha_cita" value="{{ $cita->fecha_cita }}">
                <input type="hidden" name="hora_cita" value="{{ $cita->hora_cita }}">
                <input type="hidden" name="descuento" value="{{ $cita->descuento ?? 0 }}">
                <input type="hidden" name="observaciones" value="{{ $cita->observaciones }}">
                @foreach($cita->servicios as $srv)
                    <input type="hidden" name="servicios[{{ $loop->index }}][id_servicio]" value="{{ $srv->id_servicio }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][precio_snapshot]" value="{{ $srv->pivot->precio_snapshot ?? $srv->precio }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][duracion_snapshot]" value="{{ $srv->pivot->duracion_snapshot ?? $srv->duracion_minutos }}">
                    <input type="hidden" name="servicios[{{ $loop->index }}][id_empleado]" value="{{ $srv->pivot->id_empleado ?? $cita->empleado_id }}">
                @endforeach
                <div class="mb-3">
                    <label for="metodo_pago" class="block text-xs font-bold text-gray-600 mb-2">Método de Pago <span class="text-red-500">*</span></label>
                    <select name="metodo_pago" id="metodo_pago" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-200">
                        <option value="">Selecciona método de pago</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta_credito">Tarjeta de crédito</option>
                        <option value="tarjeta_debito">Tarjeta de débito</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <button type="submit" onclick="" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-green-600 text-white font-bold text-sm hover:bg-black transition shadow-md text-center flex justify-center items-center">
                    Completar y Generar Venta
                </button>
            </form>
        @endif
    </div>

</div>
@endsection