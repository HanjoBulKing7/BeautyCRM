@extends('layouts.app')

@section('title', $cupon->exists ? 'Editar Cupón' : 'Nuevo Cupón')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-{{ $cupon->exists ? 'edit' : 'plus-circle' }} mr-3" style="color: rgba(201,162,74,.92)"></i>
                {{ $cupon->exists ? 'Editar Cupón' : 'Nuevo Cupón' }}
            </h1>
            <p class="text-gray-600 mt-1">
                {{ $cupon->exists ? 'Modifica los detalles del cupón' : 'Crea un nuevo cupón o promoción' }}
            </p>
        </div>

        <!-- Formulario -->
        <form action="{{ $cupon->exists ? route('admin.cupones.update', $cupon) : route('admin.cupones.store') }}"
              method="POST"
              class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 space-y-6">
            @csrf
            @if ($cupon->exists)
                @method('PUT')
            @endif

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
                        <input type="text" id="codigo" name="codigo"
                               value="{{ old('codigo', $cupon->codigo ?? '') }}"
                               placeholder="ej: PROMO2024, DESC15"
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
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre', $cupon->nombre ?? '') }}"
                               placeholder="ej: Descuento Semanal"
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
                              placeholder="Describe el cupón, términos, condiciones, etc."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                     focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">{{ old('descripcion', $cupon->descripcion ?? '') }}</textarea>
                </div>
            </fieldset>

            <!-- Tipo y Valor de Descuento -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Descuento</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tipo de Descuento -->
                    <div>
                        <label for="tipo_descuento" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-percent mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_descuento" name="tipo_descuento"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                       focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                                required>
                            <option value="porcentaje" {{ old('tipo_descuento', $cupon->tipo_descuento ?? 'porcentaje') === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                            <option value="monto" {{ old('tipo_descuento', $cupon->tipo_descuento ?? '') === 'monto' ? 'selected' : '' }}>Monto Fijo (MXN)</option>
                        </select>
                    </div>

                    <!-- Valor del Descuento -->
                    <div>
                        <label for="valor_descuento" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Valor <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="valor_descuento" name="valor_descuento" step="0.01" min="0"
                               value="{{ old('valor_descuento', $cupon->valor_descuento ?? '') }}"
                               placeholder="ej: 15 o 50.00"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]"
                               required>
                        @error('valor_descuento')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descuento Máximo -->
                <div class="mt-6">
                    <label for="descuento_maximo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cap mr-1" style="color: rgba(201,162,74,.92)"></i>
                        Descuento Máximo (MXN)
                    </label>
                    <input type="number" id="descuento_maximo" name="descuento_maximo" step="0.01" min="0"
                           value="{{ old('descuento_maximo', $cupon->descuento_maximo ?? '') }}"
                           placeholder="Límite máximo del descuento (opcional)"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                  focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    <small class="text-gray-500 block mt-1">Deja vacío para sin límite</small>
                </div>

                <!-- Monto Mínimo -->
                <div class="mt-6">
                    <label for="monto_minimo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill mr-1" style="color: rgba(201,162,74,.92)"></i>
                        Comprecompra Mínima (MXN)
                    </label>
                    <input type="number" id="monto_minimo" name="monto_minimo" step="0.01" min="0"
                           value="{{ old('monto_minimo', $cupon->monto_minimo ?? '') }}"
                           placeholder="Monto mínimo para aplicar (opcional)"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                  focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                </div>
            </fieldset>

            <!-- Validez y Usos -->
            <fieldset class="border-b pb-6">
                <legend class="text-lg font-semibold text-gray-900 mb-4">Validez y Límites</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha Inicio -->
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Fecha Inicio
                        </label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                               value="{{ old('fecha_inicio', optional($cupon->fecha_inicio)->format('Y-m-d') ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-times mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Fecha Fin
                        </label>
                        <input type="date" id="fecha_fin" name="fecha_fin"
                               value="{{ old('fecha_fin', optional($cupon->fecha_fin)->format('Y-m-d') ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    </div>
                </div>

                <!-- Cantidad de Usos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="cantidad_usos" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-redo mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Usos Totales Máximos
                        </label>
                        <input type="number" id="cantidad_usos" name="cantidad_usos" min="1"
                               value="{{ old('cantidad_usos', $cupon->cantidad_usos ?? '') }}"
                               placeholder="Deja vacío para ilimitado"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 transition
                                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    </div>

                    <div>
                        <label for="cantidad_por_cliente" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-check mr-1" style="color: rgba(201,162,74,.92)"></i>
                            Usos por Cliente <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="cantidad_por_cliente" name="cantidad_por_cliente" min="1"
                               value="{{ old('cantidad_por_cliente', $cupon->cantidad_por_cliente ?? 1) }}"
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
                           {{ old('aplica_cumpleaños', $cupon->aplica_cumpleaños ?? false) ? 'checked' : '' }}
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
                    <label class="flex items-center p-3 rounded-lg border {{ old('estado', $cupon->estado ?? 'activo') === 'activo' ? 'border-green-500 bg-green-50' : 'border-gray-200' }} cursor-pointer">
                        <input type="radio" name="estado" value="activo"
                               {{ old('estado', $cupon->estado ?? 'activo') === 'activo' ? 'checked' : '' }}
                               class="w-4 h-4">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Activo</span>
                            <p class="text-sm text-gray-600">El cupón está disponible para usar</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-lg border {{ old('estado', $cupon->estado ?? 'activo') === 'inactivo' ? 'border-gray-400 bg-gray-50' : 'border-gray-200' }} cursor-pointer">
                        <input type="radio" name="estado" value="inactivo"
                               {{ old('estado', $cupon->estado ?? 'activo') === 'inactivo' ? 'checked' : '' }}
                               class="w-4 h-4">
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
                    {{ $cupon->exists ? 'Actualizar Cupón' : 'Crear Cupón' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
