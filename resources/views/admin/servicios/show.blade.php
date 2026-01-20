@extends('layouts.app')
@section('title', ($servicio->nombre_servicio ?? 'Servicio') . ' - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <!-- Header (antes rosa) => Glass dorado estilo dashboard -->
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
                <i class="fas fa-spa text-xl" style="color: rgba(17,24,39,.90)"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $servicio->nombre_servicio }}</h1>
                <p class="text-gray-600 text-sm">Detalles del servicio</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Imagen del servicio --}}
        @if(!empty($servicio->imagen))
            <div class="flex justify-center">
                <div class="w-[320px] rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <img
                        src="{{ asset('storage/' . $servicio->imagen) }}"
                        alt="Foto del servicio {{ $servicio->nombre_servicio }}"
                        class="w-full h-[180px] object-cover"
                        loading="lazy"
                    >
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Información principal -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-circle-info" style="color: rgba(201,162,74,.92)"></i>
                        Información Básica
                    </h3>

                    <div class="space-y-2 text-gray-700">
                        <p>
                            <span class="font-medium text-gray-800">Categoría:</span>
                            {{ $servicio->categoria ?? 'No especificada' }}
                        </p>

                        <p>
                            <span class="font-medium text-gray-800">Precio:</span>
                            ${{ number_format($servicio->precio, 2) }}
                        </p>

                        <p>
                            <span class="font-medium text-gray-800">Duración:</span>
                            {{ $servicio->duracion_minutos }} minutos
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-medium text-gray-800">Estado:</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $servicio->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($servicio->estado) }}
                            </span>
                        </p>

                        @if($servicio->descuento > 0)
                            <p>
                                <span class="font-medium text-gray-800">Descuento:</span>
                                ${{ number_format($servicio->descuento, 2) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Descripción y características -->
            <div class="space-y-4">
                @if($servicio->descripcion)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-left" style="color: rgba(201,162,74,.92)"></i>
                            Descripción
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $servicio->descripcion }}</p>
                    </div>
                @endif

                @if($servicio->caracteristicas)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-list" style="color: rgba(201,162,74,.92)"></i>
                            Características
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $servicio->caracteristicas }}</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">

            <!-- Editar (dorado tipo dashboard) -->
            <a href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}"
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
                Editar Servicio
            </a>

            <!-- Eliminar (mantener rojo UX) -->
            <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold
                               flex items-center justify-center gap-2 transition shadow-sm hover:shadow-md"
                        onclick="return confirm('¿Estás seguro de eliminar este servicio?')">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
            </form>

            <!-- Volver -->
            <a href="{{ route('admin.servicios.index') }}"
               class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                      flex items-center justify-center gap-2 transition">
                <i class="fas fa-arrow-left" style="color: rgba(17,24,39,.70)"></i>
                Volver
            </a>
        </div>

    </div>
</div>
@endsection
