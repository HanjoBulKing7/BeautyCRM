@extends('layouts.app')
@section('title','Nuevo Cliente - Salón de Belleza')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 text-pink-700">
                    <i class="fas fa-user-plus"></i>
                </span>
                Nuevo Cliente
            </h1>
            <p class="text-gray-600 mt-1">Registra un nuevo cliente para el salón</p>
        </div>

        <a href="{{ route('admin.clientes.index') }}"
           class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-pink-50 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Información del cliente</h2>
            <p class="text-sm text-gray-600 mt-1">Completa los campos obligatorios (*)</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
                @csrf

                @include('clientes._form', ['cliente' => null])

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-400">
                        <i class="fas fa-save"></i>
                        Guardar Cliente
                    </button>

                    <a href="{{ route('admin.clientes.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
