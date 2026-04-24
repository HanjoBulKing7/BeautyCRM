@csrf

<style>
    /* Efecto Glass Premium y utili            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Teléfono</label>
                <input type="tel" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" inputmode="tel" autocomplete="tel" class="bb-input" placeholder="Número de contacto">
                @error('telefono') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

        </div>de servicios/productos) */
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
            <i class="fas fa-user text-[rgba(201,162,74,1)]"></i> Datos del Cliente
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Nombre Completo <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" class="bb-input" required placeholder="Ej: Juan Pérez">
                @error('nombre') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $cliente->email ?? '') }}" class="bb-input" placeholder="ejemplo@correo.com">
                @error('email') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700 ml-1">Teléfono</label>
                <input type="tel" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" inputmode="tel" autocomplete="tel" class="bb-input" placeholder="Número de contacto">
                @error('telefono') <p class="text-red-500 text-xs ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

          

        </div>
    </div>

</div>