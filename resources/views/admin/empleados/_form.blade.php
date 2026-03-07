<style>
    /* Efecto Glass Premium y utilidades (Homologado) */
    .bb-glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    
    .bb-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 0.75rem 1.25rem;
        transition: all 0.25s ease;
    }
    
    .bb-input:focus {
        outline: none;
        border-color: rgba(201,162,74,0.6);
        box-shadow: 0 0 0 4px rgba(201,162,74,0.15);
        background: #ffffff;
    }
</style>

<div class="space-y-6 max-w-5xl mx-auto">

    <div class="bb-glass-card p-6 md:p-8 rounded-[2rem]">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="fas fa-id-badge text-[rgba(201,162,74,1)]"></i> Datos del Empleado
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required value="{{ old('nombre', $empleado->nombre ?? '') }}" class="bb-input" placeholder="Nombre del empleado">
                @error('nombre') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido" required value="{{ old('apellido', $empleado->apellido ?? '') }}" class="bb-input" placeholder="Apellido del empleado">
                @error('apellido') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $empleado->telefono ?? '') }}" class="bb-input" placeholder="Número de contacto">
                @error('telefono') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Correo Electrónico <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email', $empleado->email ?? '') }}" class="bb-input" placeholder="empleado@correo.com">
                @error('email') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            @if(Route::currentRouteName() !== 'admin.empleados.create')
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Estatus</label>
                <select name="estatus" class="bb-input">
                    <option value="activo" {{ old('estatus', $empleado->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estatus', $empleado->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estatus') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>
            @endif

            @php
                $servicios = $servicios ?? \App\Models\Servicio::orderBy('nombre_servicio')->get();

                $fromEmpleado = [];
                if (isset($empleado) && $empleado->exists) {
                    $fromEmpleado = $empleado->relationLoaded('servicios')
                        ? $empleado->servicios->pluck('id_servicio')->toArray()
                        : $empleado->servicios()->pluck('servicios.id_servicio')->toArray();
                }

                $selectedServicios = collect(old('servicios', $fromEmpleado))
                    ->map(fn($v) => (int) $v)
                    ->filter(fn($v) => $v > 0)
                    ->unique()
                    ->values();

                $serviciosMap = $servicios->keyBy('id_servicio');
            @endphp

            <div class="md:col-span-2 mt-4 pt-6 border-t border-gray-100/80" data-empleado-servicios>
                
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2 ml-1">
                        <i class="fas fa-concierge-bell text-[rgba(201,162,74,1)]"></i>
                        Especialidades del Empleado
                    </h4>
                    <span class="text-xs font-bold text-[rgba(201,162,74,1)] bg-[rgba(201,162,74,.1)] px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                        <span class="js-servicios-count">{{ $selectedServicios->count() }}</span> asignados
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <div>
                        <label class="block text-xs font-semibold mb-2 text-gray-500 uppercase tracking-wider ml-1">1. Agregar Servicio</label>
                        <select class="js-servicio-selector bb-input">
                            <option value="">— Selecciona un servicio —</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->id_servicio }}">{{ $servicio->nombre_servicio }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[11px] text-gray-400 ml-1">
                            Elígelo en la lista y se agregará automáticamente.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-2 text-gray-500 uppercase tracking-wider ml-1">2. Servicios Seleccionados</label>
                        
                        <div class="js-servicios-seleccionados flex flex-wrap gap-2 p-3 rounded-2xl bg-white/60 border border-gray-100 min-h-[52px] shadow-inner items-start">
                            @foreach($selectedServicios as $sid)
                                @php $nombre = $serviciosMap[$sid]->nombre_servicio ?? ('Servicio #'.$sid); @endphp

                                <span class="group flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold text-gray-700 bg-white border border-gray-200 shadow-sm transition-all hover:border-[rgba(201,162,74,.4)]" data-id="{{ $sid }}">
                                    <span class="max-w-[180px] truncate">{{ $nombre }}</span>
                                    
                                    <button type="button" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full w-5 h-5 flex items-center justify-center transition-colors js-remove-servicio" title="Quitar">
                                        <i class="fas fa-times text-[10px]"></i>
                                    </button>

                                    <input type="hidden" name="servicios[]" value="{{ $sid }}">
                                </span>
                            @endforeach

                            <span class="js-servicios-empty text-xs text-gray-400 w-full text-center py-2 {{ $selectedServicios->count() ? 'hidden' : '' }}">
                                Aún no has asignado ningún servicio.
                            </span>
                        </div>

                        @error('servicios') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
                        @error('servicios.*') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>