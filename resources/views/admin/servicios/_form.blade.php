@csrf

<style>
    /* Efecto Glass Premium y utilidades */
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

    /* CSS Infalible para el Switch Dorado */
    input.bb-toggle:checked + div {
        background-color: rgba(201,162,74,1) !important;
    }
    input.bb-toggle:checked + div > div {
        transform: translateX(1.25rem); /* Mueve la bolita blanca */
    }
</style>

<div class="space-y-6 max-w-5xl mx-auto">

    {{-- ==========================================
         TARJETA 1: TODOS LOS DATOS (Súper compacta)
         ========================================== --}}
    <div class="bb-glass-card p-6 md:p-8 rounded-[2rem]">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="fas fa-info-circle text-[rgba(201,162,74,1)]"></i> Información General
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Nombre del Servicio <span class="text-red-500">*</span></label>
                <input type="text" name="nombre_servicio" value="{{ old('nombre_servicio', $servicio->nombre_servicio ?? '') }}" class="bb-input" required placeholder="Ej: Corte de Cabello">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Categoría</label>
                <div class="flex gap-2 items-center">
                    <select name="id_categoria" id="id_categoria" class="bb-input flex-1">
                        <option value="">— Sin categoría —</option>
                        @foreach(($categorias ?? []) as $cat)
                            <option value="{{ $cat->id_categoria }}" {{ (string)old('id_categoria', $servicio->id_categoria ?? '') === (string)$cat->id_categoria ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    
                    <button type="button" onclick="openModalCategoria()" class="flex-shrink-0 flex items-center justify-center px-4 py-3 rounded-[1.25rem] border-2 border-[rgba(201,162,74,.3)] text-[rgba(201,162,74,1)] hover:bg-[rgba(201,162,74,.05)] hover:border-[rgba(201,162,74,.6)] transition-all font-bold text-sm h-full shadow-sm" title="Crear nueva categoría">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                @error('id_categoria') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Precio <span class="text-red-500">*</span></label>
                <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio', $servicio->precio ?? '') }}" class="bb-input" required placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Duración (min) <span class="text-red-500">*</span></label>
                <input type="number" name="duracion_minutos" min="1" value="{{ old('duracion_minutos', $servicio->duracion_minutos ?? '') }}" class="bb-input" required placeholder="Ej: 60">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="bb-input resize-none h-[88px]" placeholder="Detalles del servicio...">{{ old('descripcion', $servicio->descripcion ?? '') }}</textarea>
            </div>

            <div class="flex flex-col justify-start">
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Fotografía (Opcional)</label>
                <div class="flex items-center gap-4">
                    <input type="file" name="imagen" id="imagenInput" accept="image/*" class="bb-input text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-[rgba(201,162,74,.1)] file:text-[rgba(201,162,74,1)] hover:file:bg-[rgba(201,162,74,.2)] flex-1 h-[88px]">
                    
                    <img id="imagenPreview" src="{{ isset($servicio) && $servicio->imagen ? asset('storage/' . ltrim($servicio->imagen, '/')) : '' }}" class="{{ isset($servicio) && $servicio->imagen ? '' : 'hidden' }} w-16 h-16 rounded-full border-2 border-[rgba(201,162,74,.5)] shadow-md object-cover flex-shrink-0">
                </div>
            </div>

            @if(!empty($showEstado))
            <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-100">
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Estado del Servicio <span class="text-red-500">*</span></label>
                <select name="estado" class="bb-input md:w-1/2" required>
                    <option value="activo" {{ old('estado', $servicio->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo (Visible en catálogo)</option>
                    <option value="inactivo" {{ old('estado', $servicio->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo (Oculto)</option>
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- ==========================================
         TARJETA 2: HORARIOS (Óvalos y Dorado)
         ========================================== --}}
    <div class="bb-glass-card p-6 md:p-8 rounded-[2rem]">
        <div class="mb-6">
            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-[rgba(201,162,74,1)]"></i> Disponibilidad
            </h3>
            <p class="text-sm text-gray-500 mt-1 ml-7">Activa los días disponibles y ajusta sus horas.</p>
        </div>

        <input type="hidden" name="horarios[_present]" value="1">

        @php
            $daysMap = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 0 => 'Domingo'];
            $existingRanges = [];
            if(isset($servicio) && $servicio && $servicio->exists) {
                foreach(($servicio->horarios ?? []) as $h) {
                    $existingRanges[$h->dia_semana][] = [
                        'inicio' => substr((string)$h->hora_inicio, 0, 5),
                        'fin'    => substr((string)$h->hora_fin, 0, 5),
                    ];
                }
            }
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($daysMap as $d => $label)
                @php 
                    $hasShifts = isset($existingRanges[$d]) && count($existingRanges[$d]) > 0;
                    $shifts = $hasShifts ? $existingRanges[$d] : [['inicio' => '09:00', 'fin' => '18:00']]; 
                @endphp

                <div class="border border-white/50 bg-white/40 rounded-[2rem] p-4 shadow-sm transition-all hover:bg-white/60">
                    <div class="flex items-center justify-between mb-2 px-2">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative flex items-center">
                                <input type="checkbox" class="sr-only bb-toggle day-toggle" data-day="{{ $d }}" {{ $hasShifts ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300">
                                    <div class="absolute w-5 h-5 bg-white rounded-full shadow left-[2px] top-[2px] transition-transform duration-300"></div>
                                </div>
                            </div>
                            <span class="ml-3 font-bold text-gray-800">{{ $label }}</span>
                        </label>
                        
                        <button type="button" class="text-xs font-bold text-[rgba(201,162,74,1)] hover:text-yellow-700 bg-[rgba(201,162,74,.1)] px-3 py-1.5 rounded-full add-shift-btn {{ $hasShifts ? '' : 'hidden' }}" data-day="{{ $d }}">
                            + Turno
                        </button>
                    </div>

                    <div class="shifts-container space-y-2 {{ $hasShifts ? '' : 'hidden' }}" id="shifts-{{ $d }}">
                        @foreach($shifts as $index => $shift)
                            <div class="shift-row flex items-center justify-between gap-1 bg-white/90 px-4 py-2 rounded-full border border-white shadow-sm">
                                <input type="time" name="horarios[{{ $d }}][{{ $index }}][hora_inicio]" value="{{ $shift['inicio'] }}" class="bg-transparent border-none text-sm font-medium focus:ring-0 text-center w-full p-0" {{ $hasShifts ? '' : 'disabled' }}>
                                <span class="text-gray-400 font-bold px-1">-</span>
                                <input type="time" name="horarios[{{ $d }}][{{ $index }}][hora_fin]" value="{{ $shift['fin'] }}" class="bg-transparent border-none text-sm font-medium focus:ring-0 text-center w-full p-0" {{ $hasShifts ? '' : 'disabled' }}>
                                
                                <button type="button" class="text-red-400 hover:text-red-600 px-2 delete-shift-btn" title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- =========================
    MODAL NUEVA CATEGORÍA
========================= --}}
<div id="modalCategoria" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity">
    <div class="bb-glass-card rounded-[2.5rem] p-8 w-full max-w-sm mx-4 transform scale-100 transition-transform">
        <h3 class="text-xl font-black text-gray-900 mb-2">Nueva Categoría</h3>
        <p class="text-sm text-gray-500 mb-6">Agrega una categoría al catálogo sin salir de esta pantalla.</p>
        
        <div class="mb-6">
            <input type="text" id="nuevaCategoriaNombre" class="bb-input bg-white/80" placeholder="Ej: Tratamientos Faciales">
            <p id="errorCategoria" class="text-red-500 text-xs ml-2 mt-2 hidden">Escribe un nombre por favor.</p>
        </div>

        <div class="flex flex-col gap-3">
            <button type="button" onclick="guardarCategoria()" id="btnGuardarCat" class="w-full py-3.5 bg-gray-900 text-white rounded-2xl font-bold hover:bg-black transition-all shadow-lg flex items-center justify-center">
                Guardar Categoría
            </button>
            <button type="button" onclick="closeModalCategoria()" class="w-full py-3.5 text-gray-600 bg-white/50 rounded-2xl font-bold hover:bg-white/80 transition-all">
                Cancelar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1. HORARIOS: Lógica de Toggles
    ========================================= */
    const toggles = document.querySelectorAll('.day-toggle');
    const addShiftBtns = document.querySelectorAll('.add-shift-btn');

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const day = this.dataset.day;
            const container = document.getElementById(`shifts-${day}`);
            const addBtn = document.querySelector(`.add-shift-btn[data-day="${day}"]`);
            const inputs = container.querySelectorAll('input[type="time"]');

            if (this.checked) {
                container.classList.remove('hidden');
                addBtn.classList.remove('hidden');
                inputs.forEach(input => input.disabled = false);
            } else {
                container.classList.add('hidden');
                addBtn.classList.add('hidden');
                inputs.forEach(input => input.disabled = true);
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-shift-btn')) {
            const row = e.target.closest('.shift-row');
            const container = row.parentElement;
            if (container.querySelectorAll('.shift-row').length > 1) {
                row.remove();
                reindexShifts(container);
            } else {
                alert("Si no hay servicio este día, apaga el interruptor principal del día.");
            }
        }
    });

    addShiftBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const day = this.dataset.day;
            const container = document.getElementById(`shifts-${day}`);
            const index = container.querySelectorAll('.shift-row').length;
            
            // Nuevo input ovalado inyectado por JS
            const html = `
                <div class="shift-row flex items-center justify-between gap-1 bg-white/90 px-4 py-2 rounded-full border border-white shadow-sm mt-2">
                    <input type="time" name="horarios[${day}][${index}][hora_inicio]" value="14:00" class="bg-transparent border-none text-sm font-medium focus:ring-0 text-center w-full p-0">
                    <span class="text-gray-400 font-bold px-1">-</span>
                    <input type="time" name="horarios[${day}][${index}][hora_fin]" value="18:00" class="bg-transparent border-none text-sm font-medium focus:ring-0 text-center w-full p-0">
                    <button type="button" class="text-red-400 hover:text-red-600 px-2 delete-shift-btn"><i class="fas fa-times"></i></button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    });

    function reindexShifts(container) {
        const day = container.id.split('-')[1];
        container.querySelectorAll('.shift-row').forEach((row, index) => {
            const inputs = row.querySelectorAll('input[type="time"]');
            inputs[0].name = `horarios[${day}][${index}][hora_inicio]`;
            inputs[1].name = `horarios[${day}][${index}][hora_fin]`;
        });
    }

    /* =========================================
       2. PREVIEW DE IMAGEN PEQUEÑA (Círculo)
    ========================================= */
    const imagenInput = document.getElementById('imagenInput');
    const imagenPreview = document.getElementById('imagenPreview');
    if(imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagenPreview.src = e.target.result;
                    imagenPreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    }
});

/* =========================================
   3. MODAL CATEGORÍA
========================================= */
function openModalCategoria() {
    document.getElementById('modalCategoria').classList.remove('hidden');
    document.getElementById('nuevaCategoriaNombre').focus();
}

function closeModalCategoria() {
    document.getElementById('modalCategoria').classList.add('hidden');
    document.getElementById('nuevaCategoriaNombre').value = '';
    document.getElementById('errorCategoria').classList.add('hidden');
}

async function guardarCategoria() {
    const nombre = document.getElementById('nuevaCategoriaNombre').value.trim();
    const errorMsg = document.getElementById('errorCategoria');
    const btnGuardar = document.getElementById('btnGuardarCat');

    if (!nombre) {
        errorMsg.classList.remove('hidden');
        return;
    }

    errorMsg.classList.add('hidden');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';

    try {
        const response = await fetch("{{ url('/admin/categoriaservicios/ajax') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre: nombre, estado: 'activo' })
        });

        const data = await response.json();

        if (response.ok) {
            const select = document.getElementById('id_categoria');
            const newOption = new Option(data.nombre, data.id_categoria, true, true);
            select.add(newOption);
            closeModalCategoria();
        } else {
            alert("Error: " + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Hubo un problema de conexión.');
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = 'Guardar Categoría';
    }
}
</script>