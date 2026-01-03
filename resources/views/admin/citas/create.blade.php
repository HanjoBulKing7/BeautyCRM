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
                                <label for="id_cliente" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>Cliente <span class="text-red-500">*</span>
                                </label>
                                <select name="id_cliente" id="id_cliente" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    <option value="">Seleccionar Cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('id_cliente') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->name }} - {{ $cliente->email }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <select name="id_servicio" id="id_servicio" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
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
                                @error('id_servicio')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Empleado -->
                            <div>
                                <label for="id_empleado" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tie text-gray-400 mr-1"></i>Empleado
                                </label>
                                <select name="id_empleado" id="id_empleado"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    <option value="">No asignado</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" {{ old('id_empleado') == $empleado->id ? 'selected' : '' }}>
                                            {{ $empleado->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_empleado')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
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
                                        <p class="font-bold text-blue-700">
                                            <span id="duracion-display">0</span> min
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

    document.getElementById('id_servicio').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const duracion = selectedOption.getAttribute('data-duracion') || 60;

        const duracionContainer = document.getElementById('duracion-container');
        const duracionDisplay = document.getElementById('duracion-display');
        const horaFinDisplay = document.getElementById('hora-fin-display');

        if (duracion > 0) {
            duracionContainer.classList.remove('hidden');
            duracionDisplay.textContent = duracion;

            const horaSelect = document.getElementById('hora_cita');
            horaSelect.addEventListener('change', function() {
                if (this.value) {
                    const horaInicio = this.value;
                    const [horas, minutos] = horaInicio.split(':').map(Number);

                    const fechaInicio = new Date();
                    fechaInicio.setHours(horas, minutos, 0, 0);

                    const fechaFin = new Date(fechaInicio.getTime() + duracion * 60000);
                    const horaFin = fechaFin.toTimeString().substring(0, 5);

                    horaFinDisplay.textContent = horaFin;
                }
            });
        } else {
            duracionContainer.classList.add('hidden');
        }
    });

    const servicioSelect = document.getElementById('id_servicio');
    if (servicioSelect.value) servicioSelect.dispatchEvent(new Event('change'));

    const horaSelect = document.getElementById('hora_cita');
    if (horaSelect.value && servicioSelect.value) horaSelect.dispatchEvent(new Event('change'));

    document.querySelector('form').addEventListener('submit', function(e) {
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
