@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.productoventa.index') }}" class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900">Editar Venta</h1>
            <p class="text-sm text-gray-500">Modificando registro de venta #{{ $venta->id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.productoventa.update', $venta->id) }}" method="POST">
        @method('PUT')
        @include('admin.productoventa._form')

        <div class="flex items-center justify-end gap-4 mt-8">
            <a href="{{ route('admin.productoventa.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-all">
                Cancelar cambios
            </a>
            <button type="submit" class="px-10 py-3 bg-[rgba(201,162,74,1)] text-white rounded-2xl font-bold shadow-lg hover:brightness-90 transform hover:-translate-y-1 transition-all">
                Actualizar Registro
            </button>
        </div>
    </form>
</div>
@endsection