{{-- resources/views/admin/citas/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📅 Cita #{{ $cita->id_cita }}</h1>
                <p class="text-gray-600 mt-1">Detalles completos de la cita</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.citas.edit', $cita->id_cita) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ✏️ Editar
                </a>
                <a href="{{ route('admin.ventas.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Volver a Ventas
                </a>
            </div>
        </div>

        <!-- Tarjetas de estado -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-full mr-3">
                        <span class="text-green-600 text-xl">📅</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="text-xl font-bold capitalize text-green-600">{{ $cita->estado_cita }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-full mr-3">
                        <span class="text-blue-600 text-xl">💰</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Precio Servicio</p>
                        <p class="text-xl font-bold text-blue-600">${{ number_format($cita->servicio->precio ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-full mr-3">
                        <span class="text-purple-600 text-xl">⏰</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Duración</p>
                        <p class="text-xl font-bold">{{ $cita->servicio->duracion ?? 60 }} min</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-full mr-3">
                        @if($cita->synced_with_google)
                            <span class="text-green-600 text-xl">✅</span>
                        @else
                            <span class="text-yellow-600 text-xl">🔄</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Google Calendar</p>
                        <p class="text-xl font-bold">
                            @if($cita->synced_with_google)
                                Sincronizada
                            @else
                                No sincronizada
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información detallada -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">📋 Detalles de la Cita</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna izquierda - Información de fecha/hora -->
                    <div>
                        <h3 class="font-bold text-gray-700 mb-3">📅 Fecha y Hora</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Hora:</span>
                                <span class="font-medium">{{ $cita->hora_cita }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estado:</span>
                                <span class="font-medium capitalize 
                                    @if($cita->estado_cita == 'completada') text-green-600
                                    @elseif($cita->estado_cita == 'cancelada') text-red-600
                                    @elseif($cita->estado_cita == 'pendiente') text-yellow-600
                                    @else text-blue-600 @endif">
                                    {{ $cita->estado_cita }}
                                </span>
                            </div>
                            @if($cita->google_event_id)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Evento Google:</span>
                                <span class="font-medium text-blue-600">
                                    <a href="https://calendar.google.com/calendar/event?eid={{ $cita->google_event_id }}" 
                                       target="_blank" class="hover:underline">
                                        Ver en Calendar
                                    </a>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Columna derecha - Participantes -->
                    <div>
                        <h3 class="font-bold text-gray-700 mb-3">👥 Participantes</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-medium">{{ $cita->cliente->name ?? 'N/A' }}</span>
                            </div>
                            @if($cita->cliente->email ?? false)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email Cliente:</span>
                                <span class="font-medium">{{ $cita->cliente->email }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Empleado:</span>
                                <span class="font-medium">{{ $cita->empleado->name ?? 'No asignado' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Servicio -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3">💼 Servicio</h3>
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                        <div>
                            <p class="font-medium text-lg">{{ $cita->servicio->nombre_servicio }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $cita->servicio->descripcion ?? 'Sin descripción' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">
                                ${{ number_format($cita->servicio->precio, 2) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $cita->servicio->duracion ?? 60 }} minutos
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Observaciones -->
                @if($cita->observaciones)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3">📝 Observaciones</h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $cita->observaciones }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Información de venta si está completada -->
                @if($cita->estado_cita == 'completada' && $cita->venta)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-3">💰 Venta Generada</h3>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">Venta #{{ $cita->venta->id_venta }}</p>
                                <p class="text-sm text-gray-600">
                                    Total: ${{ number_format($cita->venta->total, 2) }} | 
                                    Pago: {{ ucfirst($cita->venta->forma_pago) }}
                                </p>
                            </div>
                            <a href="{{ route('admin.ventas.show', $cita->venta->id_venta) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Ver Venta
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Información del sistema -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p><strong>ID Cita:</strong> #{{ $cita->id_cita }}</p>
                            <p><strong>Creada el:</strong> {{ $cita->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p><strong>Actualizada el:</strong> {{ $cita->updated_at->format('d/m/Y H:i') }}</p>
                            @if($cita->synced_with_google)
                            <p><strong>Sincronizada con Google:</strong> {{ $cita->last_sync_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Para editar esta cita, haz clic en el botón "Editar" arriba
            </div>
            <div class="flex gap-2">
                @if($cita->estado_cita != 'cancelada')
                <form action="{{ route('admin.citas.update', $cita->id_cita) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="estado_cita" value="cancelada">
                    <button type="submit" 
                            onclick="return confirm('¿Estás seguro de cancelar esta cita?')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        ❌ Cancelar Cita
                    </button>
                </form>
                @endif
                
                @if($cita->estado_cita == 'pendiente' || $cita->estado_cita == 'confirmada')
                <form action="{{ route('admin.citas.update', $cita->id_cita) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="estado_cita" value="completada">
                    <button type="submit" 
                            onclick="return confirm('¿Marcar esta cita como completada? Esto generará una venta automática.')"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        ✅ Completar Cita
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection