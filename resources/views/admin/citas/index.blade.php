@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <!-- Título centrado -->
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestión de Citas</h1>
        <p class="text-gray-600">Administra todas las citas de tu salón de belleza</p>
    </div>

    <!-- Contenedor principal centrado con ancho máximo -->
    <div class="max-w-7xl mx-auto">
        <!-- Mensajes -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Estadísticas de Google Calendar -->
        @if($isConnected)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fab fa-google text-blue-500 mr-3"></i>
                    </div>
                    <div>
                        <p class="text-blue-700 font-medium">Conectado a Google Calendar</p>
                        <p class="text-blue-600 text-sm mt-1">Las citas se sincronizan automáticamente</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fab fa-google text-yellow-500 mr-3"></i>
                    </div>
                    <div>
                        <p class="text-yellow-700">
                            <a href="{{ route('admin.google.auth') }}" class="font-medium underline hover:text-yellow-800">
                                Conectar con Google Calendar
                            </a>
                            para sincronizar citas automáticamente
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tarjeta de acciones -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-lg font-semibold text-gray-800">Citas Programadas</h2>
                    <p class="text-gray-600 text-sm">Total: {{ $citas->count() }} citas</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($isConnected)
                        <a href="{{ route('admin.citas.sync-all') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>Sincronizar Todas
                        </a>
                    @endif
                    <a href="{{ route('admin.citas.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Cita
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Citas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Servicio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha y Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Empleado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Google
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($citas as $cita)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cita->cliente->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $cita->cliente->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cita->servicio->nombre }}</div>
                                    <div class="text-sm text-gray-500">${{ number_format($cita->servicio->precio, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $cita->fecha_cita->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $cita->hora_cita }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $cita->empleado ? $cita->empleado->name : 'No asignado' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($cita->estado_cita == 'confirmada') bg-green-100 text-green-800
                                        @elseif($cita->estado_cita == 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($cita->estado_cita == 'cancelada') bg-red-100 text-red-800
                                        @elseif($cita->estado_cita == 'completada') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($cita->estado_cita) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($cita->synced_with_google && $cita->google_event_id)
                                        <span class="text-green-600" title="Sincronizado el {{ $cita->last_sync_at->format('d/m/Y H:i') }}">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @elseif($isConnected)
                                        <form action="{{ route('admin.citas.sync', $cita) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                    title="Sincronizar con Google Calendar">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400" title="Conectar Google Calendar para sincronizar">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.citas.show', $cita) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.citas.edit', $cita) }}" 
                                           class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50 transition-colors"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.citas.destroy', $cita) }}" method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta cita?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($citas->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center mt-6">
                <div class="text-gray-400 text-5xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <p class="text-gray-500 text-lg font-medium mb-2">No hay citas programadas</p>
                <p class="text-gray-400 text-sm mb-4">Comienza creando tu primera cita</p>
                <a href="{{ route('admin.citas.create') }}" 
                   class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear primera cita
                </a>
            </div>
        @endif
    </div>
</div>
@endsection