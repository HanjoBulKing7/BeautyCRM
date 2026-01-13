@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Mensajes -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto mb-6">
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-4xl mx-auto mb-6">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ✅ Card principal (sin fondo extra, sin header "Nueva Cita") -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

            <!-- Header de la card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Información de la Cita</h2>
                        <p class="text-gray-600 text-sm mt-1">Completa todos los campos obligatorios (*)</p>
                    </div>
                    <a href="{{ route('admin.citas.index') }}"
                       class="mt-3 sm:mt-0 inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a Citas
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="p-6">
                <form action="{{ route('admin.citas.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Columna Izquierda -->
                        <div class="space-y-6">
                            <!-- Cliente -->
                            <div>
                                    <!-- Cliente (buscador) -->
                                    <div class="relative">
                                        <label for="cliente_search" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user text-gray-400 mr-1"></i>Cliente <span class="text-red-500">*</span>
                                        </label>

                                        {{-- Este es el valor real que se envía al backend --}}
                                        <input type="hidden" name="id_cliente" id="id_cliente" value="{{ old('id_cliente') }}" required>

                                        {{-- Input visible para buscar --}}
                                        <div class="relative">
                                            <input
                                                type="text"
                                                id="cliente_search"
                                                autocomplete="off"
                                                placeholder="Escribe para buscar… (ej. Juan)"
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                            />

                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                                <i class="fas fa-search"></i>
                                            </div>
                                        </div>

                                        {{-- Dropdown resultados --}}
                                        <div
                                            id="cliente_dropdown"
                                            class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hidden"
                                        >
                                            <div id="cliente_results" class="max-h-60 overflow-auto"></div>
                                        </div>

                                        @error('id_cliente')
                                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                @error('id_cliente')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Servicio -->
                            <div>
                                <label for="id_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-spa text-gray-400 mr-1"></i>Servicio <span class="text-red-500">*</span>
                                </label>

                                @php
                                    // Extra servicios (si el form regresó con errores)
                                    $oldExtras = array_values(array_filter((array) old('id_servicios', [])));
                                @endphp

                                <div id="servicios-wrapper" class="space-y-3">
                                    <!-- Servicio principal (legacy compatible) -->
                                    <div class="servicio-row flex items-start gap-2" data-row="primary">
                                        <div class="relative flex-1">
                                            <select name="id_servicio" id="id_servicio" required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none servicio-select"
                                                data-role="servicio">
                                                <option value="">Seleccionar Servicio</option>
                                                @foreach($servicios as $servicio)
                                                    <option value="{{ $servicio->id_servicio }}"
                                                            data-duracion="{{ $servicio->duracion_minutos ?? 60 }}"
                                                            data-precio="{{ $servicio->precio }}"
                                                            {{ old('id_servicio') == $servicio->id_servicio ? 'selected' : '' }}>
                                                        {{ $servicio->nombre_servicio }} - ${{ number_format($servicio->precio, 2) }}
                                                        ({{ $servicio->duracion_minutos ?? 60 }} min)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Servicios extra (multi-servicio) -->
                                    @foreach($oldExtras as $idx => $oldId)
                                        <div class="servicio-row flex items-start gap-2" data-row="extra">
                                            <div class="relative flex-1">
                                                <select name="id_servicios[]" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none servicio-select"
                                                    data-role="servicio">
                                                    <option value="">Seleccionar Servicio</option>
                                                    @foreach($servicios as $servicio)
                                                        <option value="{{ $servicio->id_servicio }}"
                                                                data-duracion="{{ $servicio->duracion_minutos ?? 60 }}"
                                                                data-precio="{{ $servicio->precio }}"
                                                                {{ (string)$oldId === (string)$servicio->id_servicio ? 'selected' : '' }}>
                                                            {{ $servicio->nombre_servicio }} - ${{ number_format($servicio->precio, 2) }}
                                                            ({{ $servicio->duracion_minutos ?? 60 }} min)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                                </div>
                                            </div>

                                            <button type="button"
                                                class="remove-servicio inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fas fa-times mr-1"></i>Quitar
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" id="btn-add-servicio"
                                    class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                    <i class="fas fa-plus-circle mr-2"></i>Agregar otro servicio
                                </button>

                                @error('id_servicio')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                                @error('id_servicios')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                                @error('id_servicios.*')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            <!-- Empleado -->
                            {{-- Empleado --}}
                            <div>
                                <label for="id_empleado" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tie text-gray-400 mr-1"></i>Empleado
                                </label>
                                @php
                                $empleadosAgrupados = $empleados->groupBy(function ($e) {
                                    return $e->departamento ?: 'Sin departamento';
                                });
                                @endphp
                                <select name="id_empleado" id="id_empleado" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                                <option value="">No asignado</option>
                                @foreach($empleadosAgrupados as $departamento => $grupo)
                                    <optgroup label="{{ $departamento }}">
                                    @foreach($grupo as $empleado)
                                        <option value="{{ $empleado->id }}" {{ old('id_empleado') == $empleado->id ? 'selected' : '' }}>
                                        {{ trim(($empleado->nombre ?? '').' '.($empleado->apellido ?? '')) }}
                                        {{ $empleado->email ? ' - '.$empleado->email : '' }}
                                        </option>
                                    @endforeach
                                    </optgroup>
                                @endforeach
                                </select>
                                @error('id_empleado')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-clipboard-check text-gray-400 mr-1"></i>Estado <span class="text-red-500">*</span>
                                </label>
                                <select name="estado_cita" id="estado_cita" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    <option value="pendiente" {{ old('estado_cita') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="confirmada" {{ old('estado_cita') == 'confirmada' ? 'selected' : '' }}>✅ Confirmada</option>
                                    <option value="completada" {{ old('estado_cita') == 'completada' ? 'selected' : '' }}>🎯 Completada</option>
                                </select>
                                @error('estado_cita')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-6">
                            <!-- Fecha -->
                            <div>
                                <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="far fa-calendar-alt text-gray-400 mr-1"></i>Fecha de la Cita <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           name="fecha_cita"
                                           id="fecha_cita"
                                           required
                                           readonly
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors flatpickr-input"
                                           placeholder="Seleccionar fecha"
                                           value="{{ old('fecha_cita') }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                </div>
                                @error('fecha_cita')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Hora -->
                            <div>
                                <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="far fa-clock text-gray-400 mr-1"></i>Hora de la Cita <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="hora_cita" id="hora_cita" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none">
                                        <option value="">Seleccionar Hora</option>
                                        @php
                                            for($hora = 8; $hora <= 20; $hora++) {
                                                for($minuto = 0; $minuto < 60; $minuto += 30) {
                                                    $time = sprintf('%02d:%02d', $hora, $minuto);
                                                    $display_time = date('g:i A', strtotime($time));
                                                    echo "<option value='$time' " . (old('hora_cita') == $time ? 'selected' : '') . ">$display_time</option>";
                                                }
                                            }
                                        @endphp
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                @error('hora_cita')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Info servicio -->
                            <div id="duracion-container" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 hidden">
                                <h3 class="font-semibold text-blue-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>Información del Servicio
                                </h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 text-center">
                                        <p class="text-xs text-gray-500">Duración</p>
                                        <p class="font-bold text-blue-700 flex items-center justify-center gap-2">
                                            <input
                                                type="number"
                                                min="1"
                                                max="600"
                                                step="1"
                                                name="duracion_total_minutos"
                                                id="duracion-input"
                                                value="{{ old('duracion_total_minutos', 0) }}"
                                                class="w-20 text-center bg-white border border-blue-200 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                aria-label="Duración total de la cita en minutos"
                                            />
                                            <span>min</span>
                                        </p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 text-center">
                                        <p class="text-xs text-gray-500">Hora de Fin</p>
                                        <p class="font-bold text-blue-700" id="hora-fin-display">--:--</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="far fa-sticky-note text-gray-400 mr-1"></i>Observaciones
                                </label>
                                <textarea name="observaciones"
                                          id="observaciones"
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
                                          placeholder="Notas adicionales sobre la cita...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Descuento -->
                                <div>
                                <label for="descuento" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag text-gray-400 mr-1"></i>Descuento
                                </label>

                                <input
                                    type="number"
                                    name="descuento"
                                    id="descuento"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('descuento', $cita->descuento ?? 0) }}"
                                    placeholder="0.00"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    Monto en pesos (ej. 50.00). Si no aplica, deja en 0.
                                </p>

                                @error('descuento')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                                </div>

                        </div>
                    </div>
                    <!-- Botones -->
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('admin.citas.index') }}"
                               class="inline-flex justify-center items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center items-center bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-8 py-3 rounded-lg font-medium transition-all transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                                <i class="fas fa-calendar-plus mr-2"></i>Crear Cita
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Información de Google Calendar -->
        <div class="mt-6">
            @if($isConnected = \App\Models\GoogleToken::where('user_id', auth()->id())->exists())
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fab fa-google text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-green-700 font-medium">
                                <i class="fas fa-sync-alt mr-2"></i>Sincronización automática activada
                            </p>
                            <p class="text-green-600 text-sm mt-1">
                                Esta cita se sincronizará automáticamente con Google Calendar
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fab fa-google text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-yellow-700">
                                <a href="{{ route('admin.google.auth') }}"
                                   class="font-medium underline hover:text-yellow-800 transition-colors">
                                    <i class="fas fa-plug mr-1"></i>Conectar con Google Calendar
                                </a>
                                para sincronizar automáticamente las citas
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@php
    $clientesForJs = $clientes->map(function ($c) {
        return [
            'id' => $c->id,
            'label' => trim((($c->nombre ?? $c->name ?? '') . ' - ' . ($c->email ?? ''))),
            'nombre' => ($c->nombre ?? $c->name ?? ''),
            'email' => ($c->email ?? ''),
        ];
    })->values();
@endphp

<!-- Añadir los scripts de Flatpickr al final del layout -->
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaPicker = flatpickr("#fecha_cita", {
        locale: "es",
        minDate: "today",
        dateFormat: "Y-m-d",
        disableMobile: true,
        onChange: function(selectedDates, dateStr, instance) {
            console.log("Fecha seleccionada:", dateStr);
        }
    });

    
    // ===========================
    // Multi-servicio (UI)
    // ===========================
    const serviciosWrapper = document.getElementById('servicios-wrapper');
    const btnAddServicio = document.getElementById('btn-add-servicio');
    const duracionContainer = document.getElementById('duracion-container');
    const duracionInput = document.getElementById('duracion-input');
    const horaFinDisplay = document.getElementById('hora-fin-display');
    const horaSelect = document.getElementById('hora_cita');

    // Permitir que el usuario ajuste manualmente la duración total.
    // Si escribe, se respeta y se recalcula la hora fin con ese valor.
    if (duracionInput) {
        const oldVal = parseInt(duracionInput.value || '0', 10);
        if (!isNaN(oldVal) && oldVal > 0) duracionInput.dataset.manual = '1';

        duracionInput.addEventListener('input', () => {
            duracionInput.dataset.manual = '1';
            updateDuracionUI();
        });
    }


    function getTotalDuracionMin() {
        const selects = serviciosWrapper.querySelectorAll('select[data-role="servicio"]');
        let total = 0;

        selects.forEach(sel => {
            const opt = sel.options[sel.selectedIndex];
            const dur = parseInt(opt?.dataset?.duracion || '0', 10);
            total += isNaN(dur) ? 0 : dur;
        });

        return total;
    }

    function updateDuracionUI() {
        const autoMin = getTotalDuracionMin();

        // Duración efectiva: si el usuario escribió una duración, se usa esa; si no, la suma automática
        let totalMin = autoMin;
        if (duracionInput) {
            const typed = parseInt(duracionInput.value || '0', 10);
            if (!isNaN(typed) && typed > 0) totalMin = typed;
        }

        // Mostrar/ocultar info
        if (totalMin > 0) {
            duracionContainer.classList.remove('hidden');
            if (duracionInput && !duracionInput.dataset.manual) { duracionInput.value = totalMin; }
        } else {
            duracionContainer.classList.add('hidden');
            horaFinDisplay.textContent = '--:--';
            return;
        }

        // Calcular hora fin si hay hora seleccionada
        if (horaSelect && horaSelect.value) {
            const [horas, minutos] = horaSelect.value.split(':').map(Number);

            const fechaInicio = new Date();
            fechaInicio.setHours(horas, minutos || 0, 0, 0);

            const fechaFin = new Date(fechaInicio.getTime() + totalMin * 60000);
            const horaFin = fechaFin.toTimeString().substring(0, 5);

            horaFinDisplay.textContent = horaFin;
        } else {
            horaFinDisplay.textContent = '--:--';
        }
    }

    // Delegación: cambios en cualquier select de servicio
    serviciosWrapper.addEventListener('change', function(e) {
        if (e.target && e.target.matches('select[data-role="servicio"]')) {
            updateDuracionUI();
        }
    });

    // Cambios en hora
    if (horaSelect) {
        horaSelect.addEventListener('change', updateDuracionUI);
    }

    // Quitar servicio (delegación)
    serviciosWrapper.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-servicio');
        if (!btn) return;

        const row = btn.closest('.servicio-row');
        if (row) {
            row.remove();
            updateDuracionUI();
        }
    });

    // Agregar otro servicio
    if (btnAddServicio) {
        btnAddServicio.addEventListener('click', function() {
            const primaryRow = serviciosWrapper.querySelector('.servicio-row[data-row="primary"]');
            if (!primaryRow) return;

            const cloneRow = primaryRow.cloneNode(true);
            cloneRow.setAttribute('data-row', 'extra');

            const cloneSelect = cloneRow.querySelector('select');
            if (!cloneSelect) return;

            // Extra: name[] para multi-servicio. Sin required y sin id duplicado.
            cloneSelect.name = 'id_servicios[]';
            cloneSelect.required = false;
            cloneSelect.removeAttribute('id');
            cloneSelect.selectedIndex = 0;

            // Agregar botón quitar
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-servicio inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm font-medium transition-colors';
            removeBtn.innerHTML = '<i class="fas fa-times mr-1"></i>Quitar';

            // Asegurar layout: row = flex gap-2
            cloneRow.classList.add('flex', 'items-start', 'gap-2');

            // Si el row trae un botón previo (no debería en primary), lo removemos
            const oldRemove = cloneRow.querySelector('.remove-servicio');
            if (oldRemove) oldRemove.remove();

            cloneRow.appendChild(removeBtn);

            serviciosWrapper.appendChild(cloneRow);
            updateDuracionUI();
        });
    }

    // Recalc inicial
    updateDuracionUI();

    // Lista de clientes desde Blade → JS
    const CLIENTES = @json($clientesForJs);

const input    = document.getElementById('cliente_search');
  const dropdown = document.getElementById('cliente_dropdown');
  const results  = document.getElementById('cliente_results');
  const hidden   = document.getElementById('id_cliente');

  if (!input || !dropdown || !results || !hidden) return;

  function hideDropdown() {
    dropdown.classList.add('hidden');
    results.innerHTML = '';
  }

  function showDropdown(items) {
    if (!items.length) {
      results.innerHTML = `
        <div class="px-4 py-3 text-sm text-gray-500">
          Sin resultados
        </div>
      `;
      dropdown.classList.remove('hidden');
      return;
    }

    results.innerHTML = items.map(c => `
      <button type="button"
        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm"
        data-id="${c.id}"
        data-label="${escapeHtml(c.label || '')}"
      >
        <div class="font-medium text-gray-800">${escapeHtml(c.nombre || 'Sin nombre')}</div>
        ${c.email ? `<div class="text-gray-500">${escapeHtml(c.email)}</div>` : ''}
      </button>
    `).join('');

    dropdown.classList.remove('hidden');
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // Buscar mientras escribe
  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();

    // Si cambia el texto, invalida selección previa
    hidden.value = '';

    if (!q) {
      hideDropdown();
      return;
    }

    const filtered = (window.CLIENTES || CLIENTES || [])
      .filter(c =>
        (c.nombre || '').toLowerCase().includes(q) ||
        (c.email  || '').toLowerCase().includes(q)
      )
      .slice(0, 10);

    showDropdown(filtered);
  });

  // Seleccionar cliente
  results.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-id]');
    if (!btn) return;

    hidden.value = btn.dataset.id;
    input.value  = btn.dataset.label || '';
    hideDropdown();
  });

  // Cerrar al hacer click fuera
  document.addEventListener('click', (e) => {
    if (!e.target.closest('#cliente_search') && !e.target.closest('#cliente_dropdown')) {
      hideDropdown();
    }
  });

  // Si vienes con old('id_cliente'), pre-llenar el input visible
  if (hidden.value) {
    const found = (window.CLIENTES || CLIENTES || []).find(c => String(c.id) === String(hidden.value));
    if (found) input.value = found.label || (found.nombre || '');
  }
    //Finaliza busqudea de clientes SCRIPT

    document.querySelector('form').addEventListener('submit', function(e) {
        // Deshabilitar selects extra vacíos para no enviar valores "" al backend
        const extras = document.querySelectorAll('select[name="id_servicios[]"]');
        extras.forEach(sel => {
            if (!sel.value) sel.disabled = true;
        });

        const fecha = document.getElementById('fecha_cita').value;
        const hora = document.getElementById('hora_cita').value;
        const cliente = document.getElementById('id_cliente').value;
        const servicio = document.getElementById('id_servicio').value;

        if (!fecha || !hora || !cliente || !servicio) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios (*)');
            return false;
        }
    });
});
</script>
@endsection
@endsection
