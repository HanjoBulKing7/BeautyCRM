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
</style>

<div class="space-y-6 max-w-5xl mx-auto">

    <div class="bb-glass-card p-6 md:p-8 rounded-[2rem]">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="fas fa-box text-[rgba(201,162,74,1)]"></i> Información del Producto
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Nombre del Producto <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="bb-input" required maxlength="120" placeholder="Ej: Shampoo, Crema, Mascarilla">
                @error('nombre') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Categoría <span class="text-red-500">*</span></label>
                <div class="flex gap-2 items-center">
                    <select name="id_categoria" id="id_categoria" class="bb-input flex-1" required>
                        <option value="">— Selecciona una categoría —</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}" {{ (string)old('id_categoria', $producto->id_categoria ?? '') === (string)$cat->id_categoria ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    
                    <button type="button" onclick="openModalCategoriaProd()" class="flex-shrink-0 flex items-center justify-center px-4 py-3 rounded-[1.25rem] border-2 border-[rgba(201,162,74,.3)] text-[rgba(201,162,74,1)] hover:bg-[rgba(201,162,74,.05)] hover:border-[rgba(201,162,74,.6)] transition-all font-bold text-sm h-full shadow-sm" title="Crear nueva categoría">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                @error('id_categoria') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Precio <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="bb-input" required placeholder="0.00">
                @error('precio') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Estado <span class="text-red-500">*</span></label>
                <select name="estado" class="bb-input" required>
                    @php $estadoVal = old('estado', $producto->estado ?? 'activo'); @endphp
                    <option value="activo" {{ $estadoVal === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $estadoVal === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="bb-input resize-none h-[88px]" placeholder="Describe el producto (opcional)">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                @error('descripcion') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col justify-start">
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Fotografía del Producto</label>
                <div class="flex items-center gap-4">
                    <div class="flex-1 flex flex-col justify-center h-[88px]">
                        <input type="file" name="imagen" id="productoImagenInput" accept="image/*" class="bb-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[rgba(201,162,74,.1)] file:text-[rgba(201,162,74,1)] hover:file:bg-[rgba(201,162,74,.2)] w-full py-2 px-3">
                        <button type="button" id="productoImagenClear" class="text-xs text-red-400 hover:text-red-600 font-medium self-start ml-2 mt-1 hidden transition-all">
                            <i class="fas fa-times mr-1"></i> Quitar selección
                        </button>
                    </div>
                    
                    <div class="relative flex-shrink-0">
                        <img id="productoImagenPreview" src="{{ !empty($producto->imagen) ? asset('storage/' . $producto->imagen) : '' }}" class="{{ !empty($producto->imagen) ? '' : 'hidden' }} w-20 h-20 rounded-2xl border-2 border-[rgba(201,162,74,.5)] shadow-md object-cover">
                        <div id="productoImagenPlaceholder" class="{{ !empty($producto->imagen) ? 'hidden' : 'flex' }} w-20 h-20 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 items-center justify-center text-gray-400">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                    </div>
                </div>
                @error('imagen') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>
</div>

{{-- =========================
    MODAL NUEVA CATEGORÍA (Glass)
========================= --}}
<div id="modalCategoriaProd" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/40 backdrop-blur-sm transition-opacity">
    <div class="bb-glass-card rounded-[2.5rem] p-8 w-full max-w-sm mx-4 transform scale-100 transition-transform">
        <h3 class="text-xl font-black text-gray-900 mb-2">Nueva Categoría</h3>
        <p class="text-sm text-gray-500 mb-6">Agrega una categoría de productos sin salir.</p>
        
        <div class="mb-6">
            <input type="text" id="nuevaCategoriaNombreProd" class="bb-input bg-white/80" placeholder="Ej: Tratamientos Capilares">
            <p id="errorCategoriaProd" class="text-red-500 text-xs ml-2 mt-2 hidden">Escribe un nombre por favor.</p>
        </div>

        <div class="flex flex-col gap-3">
            <button type="button" onclick="guardarCategoriaProd()" id="btnGuardarCatProd" class="w-full py-3.5 bg-gray-900 text-white rounded-2xl font-bold hover:bg-black transition-all shadow-lg flex items-center justify-center">
                Guardar Categoría
            </button>
            <button type="button" onclick="closeModalCategoriaProd()" class="w-full py-3.5 text-gray-600 bg-white/50 rounded-2xl font-bold hover:bg-white/80 transition-all">
                Cancelar
            </button>
        </div>
    </div>
</div>

@once
<script>
/* =========================================
   1. PREVIEW DE IMAGEN COMPACTA
========================================= */
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('productoImagenInput');
    const img = document.getElementById('productoImagenPreview');
    const ph = document.getElementById('productoImagenPlaceholder');
    const clearBtn = document.getElementById('productoImagenClear');

    if (!input || !img || !ph) return;

    const showPreview = (src) => {
        img.src = src;
        img.classList.remove('hidden');
        ph.classList.add('hidden');
        clearBtn.classList.remove('hidden');
    };

    const showPlaceholder = () => {
        img.src = '';
        img.classList.add('hidden');
        ph.classList.remove('hidden');
        clearBtn.classList.add('hidden');
    };

    // Mostrar el botón de limpiar si ya hay imagen cargada desde el backend
    @if(!empty($producto->imagen))
        clearBtn.classList.remove('hidden');
    @endif

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;

        if (!file.type || !file.type.startsWith('image/')) {
            showPlaceholder();
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => showPreview(e.target.result);
        reader.readAsDataURL(file);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            input.value = '';
            @if(!empty($producto->imagen))
                showPreview("{{ asset('storage/' . $producto->imagen) }}");
                clearBtn.classList.add('hidden'); // Ocultar porque volvió a la original
            @else
                showPlaceholder();
            @endif
        });
    }
});

/* =========================================
   2. MODAL CATEGORÍA DE PRODUCTOS
========================================= */
function openModalCategoriaProd() {
    document.getElementById('modalCategoriaProd').classList.remove('hidden');
    document.getElementById('nuevaCategoriaNombreProd').focus();
}

function closeModalCategoriaProd() {
    document.getElementById('modalCategoriaProd').classList.add('hidden');
    document.getElementById('nuevaCategoriaNombreProd').value = '';
    document.getElementById('errorCategoriaProd').classList.add('hidden');
}

async function guardarCategoriaProd() {
    const nombre = document.getElementById('nuevaCategoriaNombreProd').value.trim();
    const errorMsg = document.getElementById('errorCategoriaProd');
    const btnGuardar = document.getElementById('btnGuardarCatProd');

    if (!nombre) {
        errorMsg.classList.remove('hidden');
        return;
    }

    errorMsg.classList.add('hidden');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';

    try {
        // NOTA: Verifica si esta URL es la correcta para productos o si comparten la misma tabla.
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
            closeModalCategoriaProd();
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
@endonce