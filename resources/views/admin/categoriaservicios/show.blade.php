@extends('layouts.app')
@section('title', ($categoria->nombre ?? 'Categoría') . ' - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <!-- Header => Glass dorado estilo dashboard -->
    <div
        class="p-6"
        style="
            background: linear-gradient(135deg, rgba(201,162,74,.14), rgba(255,255,255,.78));
            border-bottom: 1px solid rgba(201,162,74,.18);
        "
    >
        <div class="flex items-center gap-3">
            <div
                class="p-3 rounded-full border"
                style="
                    background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                    border-color: rgba(201,162,74,.22);
                    box-shadow: 0 10px 22px rgba(201,162,74,.12);
                "
            >
                <i class="fas fa-layer-group text-xl" style="color: rgba(17,24,39,.90)"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $categoria->nombre }}</h1>
                <p class="text-gray-600 text-sm">Detalles de la categoría</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Imagen --}}
        @if(!empty($categoria->imagen))
            <div class="flex justify-center">
                <div class="w-[320px] rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <img
                        src="{{ asset('storage/' . $categoria->imagen) }}"
                        alt="Foto de la categoría {{ $categoria->nombre }}"
                        class="w-full h-[180px] object-cover"
                        loading="lazy"
                    >
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Información -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-circle-info" style="color: rgba(201,162,74,.92)"></i>
                        Información Básica
                    </h3>

                    <div class="space-y-2 text-gray-700">
                        <p>
                            <span class="font-medium text-gray-800">Slug:</span>
                            {{ $categoria->slug ?? '—' }}
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-medium text-gray-800">Estado:</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ ($categoria->estado ?? 'activo') == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($categoria->estado ?? 'activo') }}
                            </span>
                        </p>

                        <p>
                            <span class="font-medium text-gray-800">Servicios asociados:</span>
                            {{ $categoria->servicios()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="space-y-4">
                @if($categoria->descripcion)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-left" style="color: rgba(201,162,74,.92)"></i>
                            Descripción
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $categoria->descripcion }}</p>
                    </div>
                @else
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-left" style="color: rgba(201,162,74,.92)"></i>
                            Descripción
                        </h3>
                        <p class="text-gray-600 leading-relaxed">—</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Botones de acción (igual que servicios) -->
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">

            <!-- Editar (dorado tipo dashboard) -->
            <a href="{{ route('admin.categoriaservicios.edit', $categoria->id_categoria) }}"
               class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition"
               style="
                    background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                    border: 1px solid rgba(201,162,74,.35);
                    box-shadow: 0 10px 22px rgba(201,162,74,.18);
                    color: #111827;
               "
               onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
               onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
            >
                <i class="fas fa-edit" style="color: rgba(17,24,39,.90)"></i>
                Editar Categoría
            </a>

            <!-- Eliminar -->
            <form action="{{ route('admin.categoriaservicios.destroy', $categoria->id_categoria) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold
                               flex items-center justify-center gap-2 transition shadow-sm hover:shadow-md"
                        onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
            </form>

            <!-- Volver -->
            <a href="{{ route('admin.categoriaservicios.index') }}"
               class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                      flex items-center justify-center gap-2 transition">
                <i class="fas fa-arrow-left" style="color: rgba(17,24,39,.70)"></i>
                Volver
            </a>
        </div>

    </div>
</div>
@endsection
