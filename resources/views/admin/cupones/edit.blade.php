@extends('layouts.app')

@section('title', 'Editar Cupón')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit mr-3" style="color: rgba(201,162,74,.92)"></i>
                Editar Cupón: {{ $cupon->codigo }}
            </h1>
            <p class="text-gray-600 mt-1">Modifica los detalles del cupón o promoción</p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('admin.cupones.update', $cupon) }}" method="POST"
              class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Información Básica -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Información Básica</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Código -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $cupon->codigo) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                               required>
                        @error('codigo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cupon->nombre) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                               required>
                        @error('nombre')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-1" style="color: rgba(201,162,74,.92)"></i>
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                     focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">{{ old('descripcion', $cupon->descripcion) }}</textarea>
                </div>
            </fieldset>

            <!-- Tipo y Valor de Descuento -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Descuento</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo_descuento" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_descuento" name="tipo_descuento"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                       focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                            <option value="porcentaje" {{ $cupon->tipo_descuento === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                            <option value="monto" {{ $cupon->tipo_descuento === 'monto' ? 'selected' : '' }}>Monto Fijo (MXN)</option>
                        </select>
                    </div>

                    <div>
                        <label for="valor_descuento" class="block text-sm font-medium text-gray-700 mb-2">
                            Valor <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="valor_descuento" name="valor_descuento" step="0.01" min="0"
                               value="{{ old('valor_descuento', $cupon->valor_descuento) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                               required>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="descuento_maximo" class="block text-sm font-medium text-gray-700 mb-2">
                        Descuento Máximo (MXN)
                    </label>
                    <input type="number" id="descuento_maximo" name="descuento_maximo" step="0.01" min="0"
                           value="{{ old('descuento_maximo', $cupon->descuento_maximo) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                  focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                </div>

                <div class="mt-6">
                    <label for="monto_minimo" class="block text-sm font-medium text-gray-700 mb-2">
                        Compra Mínima (MXN)
                    </label>
                    <input type="number" id="monto_minimo" name="monto_minimo" step="0.01" min="0"
                           value="{{ old('monto_minimo', $cupon->monto_minimo) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                  focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                </div>
            </fieldset>

            <!-- Validez y Usos -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Validez y Límites</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha Inicio
                        </label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                               value="{{ old('fecha_inicio', $cupon->fecha_inicio?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha Fin
                        </label>
                        <input type="date" id="fecha_fin" name="fecha_fin"
                               value="{{ old('fecha_fin', $cupon->fecha_fin?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="cantidad_usos" class="block text-sm font-medium text-gray-700 mb-2">
                            Usos Totales Máximos
                        </label>
                        <input type="number" id="cantidad_usos" name="cantidad_usos" min="1"
                               value="{{ old('cantidad_usos', $cupon->cantidad_usos) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                        @if ($cupon->cantidad_usos)
                            <small class="text-gray-600 block mt-1">Usado: {{ $cupon->usos_actuales }} / {{ $cupon->cantidad_usos }}</small>
                        @endif
                    </div>

                    <div>
                        <label for="cantidad_por_cliente" class="block text-sm font-medium text-gray-700 mb-2">
                            Usos por Cliente <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="cantidad_por_cliente" name="cantidad_por_cliente" min="1"
                               value="{{ old('cantidad_por_cliente', $cupon->cantidad_por_cliente) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                               required>
                    </div>
                </div>
            </fieldset>

            <!-- Opciones Especiales -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Opciones Especiales</legend>

                <label class="flex items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                    <input type="checkbox" id="aplica_cumpleaños" name="aplica_cumpleaños" value="1"
                           {{ $cupon->aplica_cumpleaños ? 'checked' : '' }}
                           class="w-4 h-4 rounded">
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">
                            <i class="fas fa-birthday-cake mr-2" style="color: rgba(201,162,74,.92)"></i>
                            Cupón de Cumpleaños
                        </h4>
                        <p class="text-sm text-gray-600 mt-1">
                            Si está activado, este cupón se aplicará automáticamente a clientes en su cumpleaños.
                        </p>
                    </div>
                </label>
            </fieldset>

            <!-- Estado -->
            <fieldset>
                <legend class="text-lg font-semibold text-gray-900 mb-4">Estado</legend>

                <div class="space-y-3">
                    <label class="flex items-center p-3 rounded-lg border {{ $cupon->estado === 'activo' ? 'border-green-500 bg-green-50' : 'border-gray-200' }} cursor-pointer">
                        <input type="radio" name="estado" value="activo" {{ $cupon->estado === 'activo' ? 'checked' : '' }} class="w-4 h-4">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Activo</span>
                            <p class="text-sm text-gray-600">El cupón está disponible para usar</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-lg border {{ $cupon->estado === 'inactivo' ? 'border-gray-400 bg-gray-50' : 'border-gray-200' }} cursor-pointer">
                        <input type="radio" name="estado" value="inactivo" {{ $cupon->estado === 'inactivo' ? 'checked' : '' }} class="w-4 h-4">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Inactivo</span>
                            <p class="text-sm text-gray-600">El cupón no está disponible temporalmente</p>
                        </div>
                    </label>
                </div>
            </fieldset>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.cupones.index') }}"
                   class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                          flex items-center justify-center gap-2 transition">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>

                <button type="submit"
                        class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition focus:outline-none"
                        style="background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                                border: 1px solid rgba(201,162,74,.35);
                                box-shadow: 0 10px 22px rgba(201,162,74,.18);
                                color: #111827;">
                    <i class="fas fa-save"></i>
                    Actualizar Cupón
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
