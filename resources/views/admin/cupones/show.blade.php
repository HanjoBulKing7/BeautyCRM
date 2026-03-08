@extends('layouts.app')

@section('title', 'Detalles del Cupón - ' . $cupon->codigo)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-ticket-alt mr-3" style="color: rgba(201,162,74,.92)"></i>
                    {{ $cupon->codigo }}
                </h1>
                <p class="text-gray-600 mt-1">{{ $cupon->nombre }}</p>
            </div>
            <div>
                @if ($cupon->estado === 'activo')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-circle mr-2"></i>
                        Inactivo
                    </span>
                @endif
            </div>
        </div>

        <!-- Card Principal -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <!-- Información Básica -->
            <div class="p-8 border-b bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Información Básica</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Código</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $cupon->codigo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nombre</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $cupon->nombre }}</p>
                    </div>
                </div>

                @if ($cupon->descripcion)
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-600 mb-1">Descripción</p>
                        <p class="text-gray-900">{{ $cupon->descripcion }}</p>
                    </div>
                @endif
            </div>

            <!-- Descuento -->
            <div class="p-8 border-b">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Descuento</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-4 rounded-lg" style="background: rgba(201,162,74,.08); border: 1px solid rgba(201,162,74,.28);">
                        <p class="text-sm text-gray-600 mb-1">Tipo</p>
                        <p class="text-2xl font-bold" style="color: rgba(201,162,74,.92);">
                            {{ $cupon->tipo_descuento === 'porcentaje' ? '%' : 'MXN' }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">{{ ucfirst($cupon->tipo_descuento) }}</p>
                    </div>

                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">Valor</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($cupon->valor_descuento, 2) }}</p>
                        <p class="text-xs text-gray-600 mt-1">Descuento base</p>
                    </div>

                    @if ($cupon->descuento_maximo)
                        <div class="p-4 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600 mb-1">Máximo</p>
                            <p class="text-2xl font-bold text-gray-900">MXN {{ number_format($cupon->descuento_maximo, 2) }}</p>
                            <p class="text-xs text-gray-600 mt-1">Cap de descuento</p>
                        </div>
                    @endif
                </div>

                @if ($cupon->monto_minimo)
                    <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <i class="fas fa-info-circle text-amber-600 mr-2"></i>
                        <strong>Compra Mínima:</strong>
                        <span class="ml-2">MXN {{ number_format($cupon->monto_minimo, 2) }}</span>
                    </div>
                @endif
            </div>

            <!-- Validez -->
            <div class="p-8 border-b">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Período de Validez</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-calendar-check mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Inicio
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $cupon->fecha_inicio ? $cupon->fecha_inicio->format('d/m/Y') : 'Sin fecha de inicio' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-calendar-times mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Fin
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $cupon->fecha_fin ? $cupon->fecha_fin->format('d/m/Y') : 'Sin expiración' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Límites de Uso -->
            <div class="p-8 border-b">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Límites de Uso</h2>

                <div class="space-y-4">
                    <!-- Total -->
                    <div class="p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-700">Usos Totales</p>
                            <span class="text-xs text-gray-500">
                                {{ $cupon->usos_actuales }} / {{ $cupon->cantidad_usos ?? 'Ilimitado' }}
                            </span>
                        </div>
                        @if ($cupon->cantidad_usos)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full"
                                     style="width: {{ ($cupon->usos_actuales / $cupon->cantidad_usos) * 100 }}%">
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-600">Ilimitado</p>
                        @endif
                    </div>

                    <!-- Por Cliente -->
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-1">Usos Permitidos por Cliente</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $cupon->cantidad_por_cliente }}</p>
                    </div>
                </div>
            </div>

            <!-- Opciones Especiales -->
            @if ($cupon->aplica_cumpleaños)
                <div class="p-8 border-b bg-blue-50">
                    <div class="flex items-center text-blue-800">
                        <i class="fas fa-birthday-cake text-2xl mr-4" style="color: rgba(201,162,74,.92)"></i>
                        <div>
                            <h3 class="font-semibold">Cupón de Cumpleaños</h3>
                            <p class="text-sm mt-1">Se aplica automáticamente a clientes en su cumpleaños</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Acciones -->
            <div class="p-8 bg-gray-50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="{{ route('admin.cupones.index') }}"
                   class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                          flex items-center justify-center gap-2 transition order-2 sm:order-1">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>

                <a href="{{ route('admin.cupones.edit', $cupon) }}"
                   class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition focus:outline-none
                          bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 order-3 sm:order-2">
                    <i class="fas fa-edit"></i>
                    Editar
                </a>

                <form action="{{ route('admin.cupones.destroy', $cupon) }}" method="POST" 
                      onsubmit="return confirm('¿Eliminar este cupón?');"
                      class="order-4 sm:order-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition
                                   bg-red-50 hover:bg-red-100 text-red-700 border border-red-200">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        <!-- Metadatos -->
        <div class="mt-6 text-xs text-gray-500 text-center">
            <p>Creado: {{ $cupon->created_at->format('d/m/Y H:i') }}</p>
            <p>Última actualización: {{ $cupon->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection
