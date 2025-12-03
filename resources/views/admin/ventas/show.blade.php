@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">🧾 Venta #{{ $venta->id_venta }}</h1>
                <p class="text-gray-600">Generada automáticamente desde la cita #{{ $venta->id_cita }}</p>
            </div>
            <a href="{{ route('admin.ventas.index') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                ← Volver al listado
            </a>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-full mr-3">
                        <span class="text-green-600 text-xl">💰</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Venta</p>
                        <p class="text-xl font-bold text-green-600">${{ number_format($venta->total, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-full mr-3">
                        <span class="text-blue-600 text-xl">💳</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Forma de Pago</p>
                        <p class="text-xl font-bold capitalize">{{ str_replace('_', ' ', $venta->forma_pago) }}</p>
                    </div>
                </div>
            </div>
            
            @if($venta->comision_empleado > 0)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-full mr-3">
                        <span class="text-purple-600 text-xl">👨‍💼</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Comisión Empleado</p>
                        <p class="text-xl font-bold text-purple-600">${{ number_format($venta->comision_empleado, 2) }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información detallada -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">📋 Detalles de la Transacción</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna izquierda - Información de venta -->
                    <div>
                        <h3 class="font-bold text-gray-700 mb-3">📊 Datos de la Venta</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID Venta:</span>
                                <span class="font-medium">#{{ $venta->id_venta }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha y Hora:</span>
                                <span class="font-medium">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Forma de Pago:</span>
                                <span class="font-medium capitalize">{{ str_replace('_', ' ', $venta->forma_pago) }}</span>
                            </div>
                            @if($venta->metodo_pago_especifico)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Método Específico:</span>
                                <span class="font-medium">{{ $venta->metodo_pago_especifico }}</span>
                            </div>
                            @endif
                            @if($venta->referencia_pago)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Referencia:</span>
                                <span class="font-medium">{{ $venta->referencia_pago }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Registrada el:</span>
                                <span class="font-medium">{{ $venta->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha - Información de la cita -->
                    <div>
                        <h3 class="font-bold text-gray-700 mb-3">📅 Datos de la Cita Origen</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID Cita:</span>
                                <span class="font-medium">#{{ $venta->cita->id_cita }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-medium">{{ $venta->cita->cliente->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Servicio:</span>
                                <span class="font-medium">{{ $venta->cita->servicio->nombre_servicio }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Precio Servicio:</span>
                                <span class="font-medium">${{ number_format($venta->cita->servicio->precio, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Empleado:</span>
                                <span class="font-medium">{{ $venta->cita->empleado->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha Cita:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($venta->cita->fecha_cita)->format('d/m/Y') }} a las {{ $venta->cita->hora_cita }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notas -->
                @if($venta->notas)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-2">📝 Notas Adicionales</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $venta->notas }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Información del sistema -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-start">
                        <span class="text-blue-500 mr-2">ℹ️</span>
                        <p class="text-sm text-gray-600">
                            <strong>Nota:</strong> Esta venta fue generada automáticamente por el sistema 
                            cuando la cita #{{ $venta->id_cita }} fue marcada como completada. 
                            Los datos no pueden ser modificados manualmente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection