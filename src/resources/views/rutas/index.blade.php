@extends('layouts.app')

@section('title', 'Rutas - El Progreso')
@section('page-title', 'Gestión de Rutas')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
  <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[75%_25%] gap-6">

    <!-- ================== COLUMNA IZQUIERDA ================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
      <!-- Encabezado -->
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-semibold flex items-center gap-2 text-gray-800">
          🚚 Gestión de Rutas
        </h2>
        <a href="{{ route('rutas.create') }}" 
           class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow transition">
          + Nueva Ruta
        </a>
      </div>

      <!-- Tabla de rutas -->
      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
          <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
              <th class="px-4 py-3 text-left">Ruta</th>
              <th class="px-4 py-3 text-center">Fecha</th>
              <th class="px-4 py-3 text-center">Vendedor Asignado</th>
              <th class="px-4 py-3 text-center text-green-600">Total Ventas</th>
              <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($rutas as $ruta)
              <tr class="hover:bg-gray-50 transition text-center">
                <!-- Nombre de ruta -->
                <td class="px-4 py-3 text-left font-medium text-gray-800">
                  {{ $ruta->nombre ?? 'Ruta #' . $ruta->id }}
                </td>

                <!-- Fecha -->
                <td class="px-4 py-3">
                  <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                    {{ \Carbon\Carbon::parse($ruta->fecha)->format('Y-m-d') }}
                  </span>
                </td>

                <!-- Empleado -->
                <td class="px-4 py-3">
                  {{ $ruta->empleado->nombre ?? 'Sin asignar' }}
                </td>

                <!-- Total -->
                <td class="px-4 py-3 font-semibold text-green-600">
                  ${{ number_format($ruta->detalles->sum('total'), 2) }}
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 flex justify-center gap-2">
                  <!-- 🔽 CAMBIO: Ahora va a rutas.show en vez de rutas.edit -->
                  <a href="{{ route('rutas.show', $ruta) }}"
                     class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-medium shadow transition flex items-center gap-1">
                    👁️ <span>Ver Detalles</span>
                  </a>
                  <form action="{{ route('rutas.destroy', $ruta) }}" method="POST" 
                        onsubmit="return confirm('¿Seguro que deseas eliminar esta ruta?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs font-medium shadow transition flex items-center gap-1">
                      🗑️ <span>Eliminar</span>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                  No hay rutas registradas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="mt-4">
        {{ $rutas->links() }}
      </div>
    </div>

    <!-- ================== COLUMNA DERECHA (REPORTES) ================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 h-fit w-full">
      <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">📊 Resumen General</h3>

      @php
        $totalGlobal = $rutas->sum(fn($r) => $r->detalles->sum('total'));
        $rutasTotales = $rutas->total();
        $promedio = $rutasTotales > 0 ? $totalGlobal / $rutasTotales : 0;
      @endphp

      <!-- Total de rutas -->
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-3 text-center">
        <p class="text-sm font-medium text-blue-800">🚛 Total de Rutas</p>
        <h4 class="text-3xl font-bold text-blue-700 mt-1">{{ $rutasTotales }}</h4>
      </div>

      <!-- Total global -->
      <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-3 text-center">
        <p class="text-sm font-medium text-green-800">💵 Total Global Vendido</p>
        <h4 class="text-3xl font-bold text-green-700 mt-1">${{ number_format($totalGlobal, 2) }}</h4>
      </div>

      <!-- Promedio -->
      <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
        <p class="text-sm font-medium text-orange-800">📈 Promedio por Ruta</p>
        <h4 class="text-3xl font-bold text-orange-700 mt-1">${{ number_format($promedio, 2) }}</h4>
      </div>
    </div>
  </div>
</div>
@endsection