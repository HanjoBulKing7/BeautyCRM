@extends('layouts.app')

@section('title', 'Reportes - CRM')

@section('page-title', 'Generar Reportes')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
   

    <!-- Tabs Navigation -->
    <div class="mb-6">
        <div class="flex space-x-4 border-b dark:border-gray-600">
            <a href="{{ route('reportes.index', array_merge(['tipo' => 'diario', 'fecha' => $fecha], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
               class="pb-4 px-2 font-medium {{ $tipo === 'diario' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Reporte Diario
            </a>
            <a href="{{ route('reportes.index', array_merge(['tipo' => 'semanal', 'fecha' => $fecha], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
               class="pb-4 px-2 font-medium {{ $tipo === 'semanal' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Reporte Semanal
            </a>
            <a href="{{ route('reportes.index', array_merge(['tipo' => 'mensual', 'fecha' => $fecha], auth()->user()->rol === 'admin' && request('sucursal_id') ? ['sucursal_id' => request('sucursal_id')] : [])) }}" 
               class="pb-4 px-2 font-medium {{ $tipo === 'mensual' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Reporte Mensual
            </a>
        </div>
    </div>

    <!-- Encabezado con título -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-2 md:mb-0">
            <i class="fas fa-chart-line mr-3 text-yellow-500"></i>
            Reportes
        </h3>
        
        <div class="flex items-center gap-2">
            <!-- Selector de sucursal (solo para administradores) -->
            @auth
                @if(auth()->user()->rol === 'admin')
                <select id="sucursal_id" class="border rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Todas las sucursales</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ request('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
                @else
                    <!-- Mostrar nombre de sucursal para usuarios no administradores -->
                    <div class="bg-gray-100 px-3 py-2 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-store mr-1 text-blue-500"></i>
                        {{ auth()->user()->sucursal->nombre ?? 'Sucursal' }}
                    </div>
                @endif
            @endauth
            
            <button onclick="actualizarReporte()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                <i class="fas fa-sync-alt mr-1"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Contenido del reporte -->
    <div class="bg-white p-4 rounded-lg border dark:bg-gray-800 dark:border-gray-700">
        @if($tipo == 'diario')
            @include('reportes.diario')
        @elseif($tipo == 'semanal')
            @include('reportes.semanal')
        @elseif($tipo == 'mensual')
            @include('reportes.mensual')
        @endif
    </div>
</div>

<script>
function actualizarReporte() {
    const tipo = '{{ $tipo }}';
    const url = new URL(window.location.href);
    
    // Obtener la fecha del selector específico de cada vista
    let fecha = '';
    
    if (tipo === 'diario') {
        const fechaInput = document.getElementById('fecha-dia');
        if (fechaInput) fecha = fechaInput.value;
    } else if (tipo === 'semanal') {
        const fechaSemana = document.getElementById('fecha-semana');
        if (fechaSemana) fecha = fechaSemana.value;
    } else if (tipo === 'mensual') {
        const fechaMes = document.getElementById('fecha-mes');
        if (fechaMes) fecha = fechaMes.value + '-01'; // Convertir YYYY-MM a YYYY-MM-01
    }
    
    if (fecha) {
        url.searchParams.set('fecha', fecha);
    }
    url.searchParams.set('tipo', tipo);
    
    // Agregar sucursal si es administrador y hay selector de sucursal
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect) {
        if (sucursalSelect.value) {
            url.searchParams.set('sucursal_id', sucursalSelect.value);
        } else {
            url.searchParams.delete('sucursal_id');
        }
    }
    
    window.location.href = url.toString();
}

// Inicializar eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const sucursalSelect = document.getElementById('sucursal_id');
    if (sucursalSelect) {
        sucursalSelect.addEventListener('change', function() {
            actualizarReporte();
        });
    }

    // También agregar evento change a los inputs de fecha
    const fechaDia = document.getElementById('fecha-dia');
    if (fechaDia) {
        fechaDia.addEventListener('change', function() {
            actualizarReporte();
        });
    }

    const fechaSemana = document.getElementById('fecha-semana');
    if (fechaSemana) {
        fechaSemana.addEventListener('change', function() {
            actualizarReporte();
        });
    }

    const fechaMes = document.getElementById('fecha-mes');
    if (fechaMes) {
        fechaMes.addEventListener('change', function() {
            actualizarReporte();
        });
    }
});
</script>
@endsection