{{-- resources/views/admin/servicios/edit.blade.php --}}
@extends('layouts.app')
@section('title','Editar Servicio - Salón de Belleza')

@section('content')
<div class="w-full max-w-none px-4 md:px-6 py-6">

    <!-- Header (sin card contenedora) -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div
                class="p-3 rounded-full border"
                style="
                    background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                    border-color: rgba(201,162,74,.22);
                    box-shadow: 0 10px 22px rgba(201,162,74,.12);
                "
            >
                <i class="fas fa-edit text-xl" style="color: rgba(17,24,39,.90)"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Servicio</h1>
                <p class="text-gray-600 text-sm">Actualice la información de {{ $servicio->nombre_servicio }}</p>
            </div>
        </div>

        <a href="{{ route('admin.servicios.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold">
            <i class="fas fa-arrow-left text-sm"></i>
            Volver
        </a>
    </div>

    <form method="POST"
          action="{{ route('admin.servicios.update', $servicio->id_servicio) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.servicios._form', [
            'servicio' => $servicio,
            'showEstado' => true
        ])

        <!-- Botones -->
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">

            <button type="submit"
                    class="px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition focus:outline-none"
                    style="
                        background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                        border: 1px solid rgba(201,162,74,.35);
                        box-shadow: 0 10px 22px rgba(201,162,74,.18);
                        color: #111827;
                    "
                    onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
                    onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
            >
                <i class="fas fa-save" style="color: rgba(17,24,39,.90)"></i>
                Actualizar Servicio
            </button>

            <a href="{{ route('admin.servicios.index') }}"
               class="px-6 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold
                      inline-flex items-center justify-center gap-2 transition">
                <i class="fas fa-times text-sm"></i>
                Cancelar
            </a>
        </div>
    </form>

</div>
@endsection
