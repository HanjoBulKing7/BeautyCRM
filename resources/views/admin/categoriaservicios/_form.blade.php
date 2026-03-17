@php
    // por si no lo mandas desde la vista
    $showEstado = $showEstado ?? false;
@endphp

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
</style>

<div class="space-y-6 max-w-5xl mx-auto">

    <div class="bb-glass-card p-6 md:p-8 rounded-[2rem]">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="fas fa-tag text-[rgba(201,162,74,1)]"></i> Información de la Categoría
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Nombre de Categoría <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" id="catNombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" class="bb-input" required placeholder="Ej: Cabello, Uñas, Facial">
                @error('nombre') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            @if($showEstado)
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" class="bb-input" required>
                        <option value="activo" {{ old('estado', $categoria->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $categoria->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
                </div>
            @else
                <input type="hidden" name="estado" value="{{ old('estado', 'activo') }}">
            @endif

            <div class="md:col-span-2 mt-2">
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Fotografía de la Categoría</label>
                
                <div class="flex items-center gap-4">
                    <div class="flex-1 flex flex-col justify-center">
                        <input type="file" name="imagen" id="catImagenInput" accept="image/*" class="bb-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[rgba(201,162,74,.1)] file:text-[rgba(201,162,74,1)] hover:file:bg-[rgba(201,162,74,.2)] w-full py-2 px-3">
                        
                        <div class="flex items-center justify-between mt-2 ml-2">
                            <p class="text-[11px] text-gray-400">Formatos: JPG/PNG/WEBP (máx 2MB).</p>
                            <button type="button" id="catImagenClear" class="text-xs text-red-400 hover:text-red-600 font-medium hidden transition-all">
                                <i class="fas fa-times mr-1"></i> Quitar
                            </button>
                        </div>
                    </div>

                    <div class="relative flex-shrink-0">
                        @php $tieneImagen = !empty($categoria->imagen_url ?? null); @endphp
                        
                        <img id="catImagenPreview" src="{{ $tieneImagen ? $categoria->imagen_url : '' }}" class="{{ $tieneImagen ? '' : 'hidden' }} w-28 md:w-32 aspect-video rounded-xl border-2 border-[rgba(201,162,74,.5)] shadow-sm object-cover">
                        
                        <div id="catImagenPlaceholder" class="{{ $tieneImagen ? 'hidden' : 'flex' }} w-28 md:w-32 aspect-video rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 items-center justify-center text-gray-400">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                    </div>
                </div>
                @error('imagen') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('catImagenInput');
    const preview = document.getElementById('catImagenPreview');
    const placeholder = document.getElementById('catImagenPlaceholder');
    const clearBtn = document.getElementById('catImagenClear');

    if (!input || !preview || !placeholder) return;

    const showPreview = (src) => {
        preview.src = src;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');
    };

    const showPlaceholder = () => {
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
    };

    @if($tieneImagen)
        if (clearBtn) clearBtn.classList.remove('hidden');
    @endif

    input.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        if (!file.type || !file.type.startsWith('image/')) {
            showPlaceholder();
            input.value = '';
            return;
        }

        const url = URL.createObjectURL(file);
        showPreview(url);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            input.value = '';
            @if($tieneImagen)
                showPreview("{{ $categoria->imagen_url }}");
                clearBtn.classList.add('hidden');
            @else
                showPlaceholder();
            @endif
        });
    }
});
</script>
@endonce