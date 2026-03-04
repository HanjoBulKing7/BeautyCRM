{{-- resources/views/admin/empleados/_form.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Nombre --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user mr-2" style="color: rgba(201,162,74,.92)"></i>
            Nombre <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nombre" required
               value="{{ old('nombre', $empleado->nombre) }}"
               class="w-full border border-gray-300 rounded-lg p-3 transition
                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
               placeholder="Nombre del empleado">
    </div>

    {{-- Apellido --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-user mr-2" style="color: rgba(201,162,74,.92)"></i>
            Apellido <span class="text-red-500">*</span>
        </label>
        <input type="text" name="apellido" required
               value="{{ old('apellido', $empleado->apellido) }}"
               class="w-full border border-gray-300 rounded-lg p-3 transition
                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
               placeholder="Apellido del empleado">
    </div>

    {{-- Teléfono --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-phone mr-2" style="color: rgba(201,162,74,.92)"></i>
            Teléfono
        </label>
        <input type="text" name="telefono"
               value="{{ old('telefono', $empleado->telefono) }}"
               class="w-full border border-gray-300 rounded-lg p-3 transition
                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
               placeholder="Número de teléfono">
    </div>

    {{-- Email --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-envelope mr-2" style="color: rgba(201,162,74,.92)"></i>
            Email <span class="text-red-500">*</span>
        </label>
        <input type="email" name="email" required
               value="{{ old('email', $empleado->email ?? '') }}"
               class="w-full border border-gray-300 rounded-lg p-3 transition
                      focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]"
               placeholder="Correo del empleado">
        @error('email')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Fecha contratación (eliminado en modo crear) --}}

    {{-- Estatus (solo activo/inactivo, oculto en modo crear) --}}
    @if(Route::currentRouteName() !== 'admin.empleados.create')
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <label class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-toggle-on mr-2" style="color: rgba(201,162,74,.92)"></i>
            Estatus
        </label>
        <select name="estatus"
                class="w-full border border-gray-300 rounded-lg p-3 transition
                       focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)] focus:border-[rgba(201,162,74,.55)]">
            <option value="activo" {{ old('estatus', $empleado->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estatus', $empleado->estatus) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('estatus')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
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

    {{-- SERVICIOS --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm md:col-span-2"
         data-empleado-servicios>

        <div class="flex items-center justify-between mb-3">
            <label class="block text-sm font-medium text-gray-700">
                <i class="fas fa-concierge-bell mr-2" style="color: rgba(201,162,74,.92)"></i>
                Servicios del empleado
            </label>

            <div class="text-xs text-gray-600">
                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full border"
                      style="border-color: rgba(201,162,74,.28); background: rgba(201,162,74,.10);">
                    <span class="js-servicios-count">{{ $selectedServicios->count() }}</span>
                    <span>seleccionados</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- 2) Servicios seleccionados --}}
            <div>
                <div class="text-xs text-gray-500 mb-2">2) Servicios seleccionados</div>

                <div class="js-servicios-seleccionados flex flex-wrap gap-2 p-3 rounded-lg border min-h-[52px]"
                     style="border-color: rgba(201,162,74,.22); background: rgba(201,162,74,.05);">
                    @foreach($selectedServicios as $sid)
                        @php $nombre = $serviciosMap[$sid]->nombre_servicio ?? ('Servicio #'.$sid); @endphp

                        <span class="flex items-center gap-2 px-3 py-1 rounded-full text-sm border"
                              style="background: rgba(201,162,74,.12); border-color: rgba(201,162,74,.35);"
                              data-id="{{ $sid }}">
                            <span class="max-w-[240px] truncate">{{ $nombre }}</span>

                            <button type="button" class="text-gray-500 hover:text-red-600 js-remove-servicio" title="Quitar">✕</button>

                            <input type="hidden" name="servicios[]" value="{{ $sid }}">
                        </span>
                    @endforeach

                    <span class="js-servicios-empty text-sm text-gray-400 {{ $selectedServicios->count() ? 'hidden' : '' }}">
                        Aún no has agregado servicios.
                    </span>
                </div>

                @error('servicios')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('servicios.*')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 3) Agregar servicio --}}
            <div>
                <div class="text-xs text-gray-500 mb-2">3) Agregar servicio</div>

                <select class="js-servicio-selector w-full border border-gray-300 rounded-lg p-3 transition
                               focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]
                               focus:border-[rgba(201,162,74,.55)]">
                    <option value="">Selecciona un servicio</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id_servicio }}">{{ $servicio->nombre_servicio }}</option>
                    @endforeach
                </select>

                <p class="mt-2 text-xs text-gray-500">
                    Selecciona un servicio y se agregará automáticamente a la lista.
                </p>
            </div>

        </div>
    </div>

</div>
