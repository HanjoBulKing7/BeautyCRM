@extends('layouts.app')
@section('title','Crear Servicio - Salón de Belleza')

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
                <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Servicio</h1>
                <p class="text-gray-600 text-sm">Agregue un nuevo servicio al catálogo del salón</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('admin.servicios.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @include('admin.servicios._form', [
                'servicio' => new App\Models\Servicio(),
                'showEstado' => false
            ])

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">

                <!-- Guardar (dorado tipo dashboard) -->
                <button type="submit"
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
                    <i class="fas fa-save" style="color: rgba(17,24,39,.90)"></i>
                    Guardar Servicio
                </button>

                <!-- Cancelar -->
                <a href="{{ route('admin.servicios.index') }}"
                   class="px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                          flex items-center justify-center gap-2 transition">
                    <i class="fas fa-times" style="color: rgba(17,24,39,.70)"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
