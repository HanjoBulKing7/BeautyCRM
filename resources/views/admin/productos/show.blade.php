@extends('layouts.app')
@section('title', ($producto->nombre ?? 'Producto') . ' - Salón de Belleza')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <div class="p-6" style="background: linear-gradient(135deg, rgba(201,162,74,.14), rgba(255,255,255,.78)); border-bottom: 1px solid rgba(201,162,74,.18);">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-full border"
                 style="background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75)); border-color: rgba(201,162,74,.22); box-shadow: 0 10px 22px rgba(201,162,74,.12);">
                <i class="fas fa-box text-xl" style="color: rgba(17,24,39,.90)"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $producto->nombre }}</h1>
                <p class="text-gray-600 text-sm">Detalles del producto</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        @if(!empty($producto->imagen))
            <div class="flex justify-center">
                <div class="w-[320px] rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <img src="{{ asset('storage/' . ltrim($producto->imagen,'/')) }}"
                         class="w-full h-[180px] object-cover"
                         alt="Imagen producto"
                         loading="lazy">
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-circle-info" style="color: rgba(201,162,74,.92)"></i>
                    Información
                </h3>

                <div class="space-y-2 text-gray-700">
                    <p><span class="font-medium text-gray-800">Categoría:</span> {{ $producto->categoria->nombre ?? '—' }}</p>
                    <p><span class="font-medium text-gray-800">Precio:</span> ${{ number_format((float)$producto->precio, 2) }}</p>
                    <p><span class="font-medium text-gray-800">Creado:</span> {{ optional($producto->created_at)->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-image" style="color: rgba(201,162,74,.92)"></i>
                    Imagen
                </h3>
                <p class="text-gray-600">
                    {{ $producto->imagen ? 'Sí (guardada en storage)' : '—' }}
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.productos.edit', $producto->id) }}"
               class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition"
               style="background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2)); border: 1px solid rgba(201,162,74,.35); box-shadow: 0 10px 22px rgba(201,162,74,.18); color: #111827;">
                <i class="fas fa-edit" style="color: rgba(17,24,39,.90)"></i>
                Editar
            </a>

            <form action="{{ route('admin.productos.destroy', $producto->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este producto?');">
                @csrf
                @method('DELETE')
                <button class="px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold flex items-center justify-center gap-2 transition shadow-sm hover:shadow-md">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
            </form>

            <a href="{{ route('admin.productos.index') }}"
               class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold flex items-center justify-center gap-2 transition">
                <i class="fas fa-arrow-left" style="color: rgba(17,24,39,.70)"></i>
                Volver
            </a>
        </div>

    </div>
</div>
@endsection
