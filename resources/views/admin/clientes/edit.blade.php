@extends('layouts.app')
@section('title','Editar Cliente - Salón de Belleza')

@section('content')
<div class="w-full max-w-none px-4 md:px-6 py-6">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border"
                    style="
                        background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                        border-color: rgba(201,162,74,.22);
                        box-shadow: 0 10px 22px rgba(201,162,74,.12);
                        color: rgba(17,24,39,.90);
                    "
                >
                    <i class="fas fa-user-edit"></i>
                </span>
                Editar Cliente
            </h1>
            <p class="text-gray-600 mt-1">
                Actualiza la información de {{ $cliente->nombre ?? 'cliente' }}
            </p>
        </div>
        <!--
        <a href="{{ route('admin.clientes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                  border border-[rgba(201,162,74,.35)]
                  bg-[rgba(255,255,255,.70)] hover:bg-white
                  text-gray-800 font-semibold shadow-sm hover:shadow transition">
            <i class="fas fa-arrow-left" style="color: rgba(201,162,74,.95)"></i>
            Volver
        </a>
        -->
    </div>

    <form method="POST" action="{{ route('admin.clientes.update', $cliente->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.clientes._form', ['cliente' => $cliente])

        <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-gray-200">

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold transition focus:outline-none"
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
                Guardar Cambios
            </button>
            
            <a href="{{ route('admin.clientes.index') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                      bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold
                      shadow-sm hover:shadow transition">
                <i class="fas fa-times" style="color: rgba(17,24,39,.70)"></i>
                Cancelar
            </a>
        </div>
    </form>

</div>
@endsection