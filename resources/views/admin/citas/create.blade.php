<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cita - BeautyCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-800">Nueva Cita</h1>
                    <a href="{{ route('admin.citas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a Citas
                    </a>
                </div>
            </div>

            <!-- Mensajes -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('admin.citas.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna Izquierda - Información de la Cita -->
                        <div class="space-y-6">
                            <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">
                                Información de la Cita
                            </h2>

                            <!-- Cliente -->
                            <div>
                                <label for="id_cliente" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cliente <span class="text-red-500">*</span>
                                </label>
                                <select name="id_cliente" id="id_cliente" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar Cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('id_cliente') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->name }} - {{ $cliente->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_cliente')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Servicio -->
                            <div>
                                <label for="id_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Servicio <span class="text-red-500">*</span>
                                </label>
                                <select name="id_servicio" id="id_servicio" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar Servicio</option>
                                    @foreach($servicios as $servicio)
                                        <option value="{{ $servicio->id_servicio }}" 
                                                data-duracion="{{ $servicio->duracion ?? 60 }}"
                                                data-precio="{{ $servicio->precio }}"
                                                {{ old('id_servicio') == $servicio->id_servicio ? 'selected' : '' }}>
                                            {{ $servicio->nombre_servicio }} - ${{ number_format($servicio->precio, 2) }} 
                                            ({{ $servicio->duracion ?? 60 }} min)
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_servicio')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Empleado -->
                            <div>
                                <label for="id_empleado" class="block text-sm font-medium text-gray-700 mb-2">
                                    Empleado
                                </label>
                                <select name="id_empleado" id="id_empleado"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">No asignado</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" {{ old('id_empleado') == $empleado->id ? 'selected' : '' }}>
                                            {{ $empleado->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_empleado')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select name="estado_cita" id="estado_cita" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="pendiente" {{ old('estado_cita') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmada" {{ old('estado_cita') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="completada" {{ old('estado_cita') == 'completada' ? 'selected' : '' }}>Completada</option>
                                </select>
                                @error('estado_cita')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna Derecha - Fecha y Hora -->
                        <div class="space-y-6">
                            <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">
                                Fecha y Hora
                            </h2>

                            <!-- Selector de Fecha -->
                            <div>
                                <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de la Cita <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="fecha_cita" 
                                       id="fecha_cita" 
                                       required
                                       readonly
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Seleccionar fecha"
                                       value="{{ old('fecha_cita') }}">
                                @error('fecha_cita')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selector de Hora -->
                            <div>
                                <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de la Cita <span class="text-red-500">*</span>
                                </label>
                                <select name="hora_cita" id="hora_cita" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar Hora</option>
                                    @php
                                        // Generar horas de 8:00 AM a 8:00 PM
                                        for($hora = 8; $hora <= 20; $hora++) {
                                            for($minuto = 0; $minuto < 60; $minuto += 30) {
                                                $time = sprintf('%02d:%02d', $hora, $minuto);
                                                $display_time = date('g:i A', strtotime($time));
                                                echo "<option value='$time' " . (old('hora_cita') == $time ? 'selected' : '') . ">$display_time</option>";
                                            }
                                        }
                                    @endphp
                                </select>
                                @error('hora_cita')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duración Calculada -->
                            <div id="duracion-container" class="bg-blue-50 border border-blue-200 rounded-md p-4 hidden">
                                <h3 class="font-semibold text-blue-800 mb-2">Información del Servicio</h3>
                                <p class="text-sm text-blue-700">
                                    Duración: <span id="duracion-display">0</span> minutos
                                </p>
                                <p class="text-sm text-blue-700">
                                    Hora de fin: <span id="hora-fin-display">--:--</span>
                                </p>
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                    Observaciones
                                </label>
                                <textarea name="observaciones" 
                                          id="observaciones" 
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Notas adicionales sobre la cita...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.citas.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition duration-200">
                            <i class="fas fa-calendar-plus mr-2"></i>Crear Cita
                        </button>
                    </div>
                </form>
            </div>

            <!-- Información de Google Calendar -->
            @if($isConnected = \App\Models\GoogleToken::where('user_id', auth()->id())->exists())
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-6">
                    <p class="text-green-700">
                        <i class="fab fa-google mr-2"></i>
                        Esta cita se sincronizará automáticamente con Google Calendar
                    </p>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-6">
                    <p class="text-yellow-700">
                        <i class="fab fa-google mr-2"></i>
                        <a href="{{ route('admin.google.auth') }}" class="underline">Conectar con Google Calendar</a> 
                        para sincronizar automáticamente las citas
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Inicializar Flatpickr para el calendario
        const fechaPicker = flatpickr("#fecha_cita", {
            locale: "es",
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Aquí podrías agregar lógica para verificar disponibilidad
                console.log("Fecha seleccionada:", dateStr);
            }
        });

        // Calcular duración y hora de fin
        document.getElementById('id_servicio').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const duracion = selectedOption.getAttribute('data-duracion') || 60;
            const precio = selectedOption.getAttribute('data-precio') || 0;
            
            const duracionContainer = document.getElementById('duracion-container');
            const duracionDisplay = document.getElementById('duracion-display');
            const horaFinDisplay = document.getElementById('hora-fin-display');
            
            if (duracion > 0) {
                duracionContainer.classList.remove('hidden');
                duracionDisplay.textContent = duracion;
                
                // Calcular hora de fin cuando se seleccione una hora
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

        // Disparar el evento change al cargar la página si ya hay un servicio seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            const servicioSelect = document.getElementById('id_servicio');
            if (servicioSelect.value) {
                servicioSelect.dispatchEvent(new Event('change'));
            }
            
            // Si hay una hora seleccionada, calcular hora de fin
            const horaSelect = document.getElementById('hora_cita');
            if (horaSelect.value && servicioSelect.value) {
                horaSelect.dispatchEvent(new Event('change'));
            }
        });

        // Validación antes de enviar el formulario
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
    </script>
</body>
</html>