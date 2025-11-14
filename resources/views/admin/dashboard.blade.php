@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-light text-gray-800">Panel de Administración</h1>
        <p class="text-gray-500 mt-1">Resumen general de tu salón de belleza</p>
    </div>

    <!-- Métricas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Tarjeta Citas Hoy -->
        <div class="card p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Citas hoy</p>
                    <p class="text-2xl font-semibold text-gray-800">8</p>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-sm text-green-600 font-medium">+2 desde ayer</span>
            </div>
        </div>
        
        <!-- El resto de tu contenido ACTUAL... -->
    </div>

    <!-- Gráficos y contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- ... todo tu contenido actual ... -->
    </div>

    <!-- Sección inferior -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ... todo tu contenido actual ... -->
    </div>
@endsection

@section('scripts')
    <!-- Solo los scripts específicos de esta página -->
    <script src="{{ asset('js/AdminDashboard.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Scripts específicos del dashboard
    </script>
@endsection