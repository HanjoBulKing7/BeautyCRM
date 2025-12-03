{{-- resources/views/admin/ventas/cita-detalle.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">📅 Cita Completada #{{ $cita->id_cita }}</h1>
            <p class="text-gray-600">Esta cita fue completada pero no generó venta automática</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-3">📋 Información de la Cita</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Cita:</span>
                            <span class="font-medium">#{{ $cita->id_cita }}</span>
                        </div>
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
                            <span class="font-medium capitalize text-green-600">Completada</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-700 mb-3">👥 Participantes</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cliente:</span>
                            <span class="font-medium">{{ $cita->cliente->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Empleado:</span>
                            <span class="font-medium">{{ $cita->empleado->name ?? 'No asignado' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-bold text-gray-700 mb-3">💼 Servicio</h3>
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="font-medium">{{ $cita->servicio->nombre_servicio }}</p>
                        <p class="text-sm text-gray-500">{{ $cita->servicio->descripcion ?? 'Sin descripción' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-green-600">
                            ${{ number_format($cita->servicio->precio, 2) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $cita->servicio->duracion ?? 60 }} minutos
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-yellow-600">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Nota:</strong> Esta cita está marcada como completada pero no se generó 
                                automáticamente una venta. Esto puede deberse a un error en el sistema o a que 
                                la cita fue marcada como completada antes de implementar la función de ventas automáticas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.ventas.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ← Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection