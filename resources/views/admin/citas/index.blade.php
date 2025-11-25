<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - BeautyCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-800">Gestión de Citas</h1>
                    <div class="flex space-x-4">
                        @if($isConnected = false)
                            <a href="{{ route('admin.citas.sync-all') }}" 
                               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                <i class="fas fa-sync-alt mr-2"></i>Sincronizar Todas
                            </a>
                        @endif
                        <a href="{{ route('admin.citas.create') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-plus mr-2"></i>Nueva Cita
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Estadísticas de Google Calendar -->
            @if($isConnected)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-blue-700">
                        <i class="fab fa-google mr-2"></i>Conectado a Google Calendar
                    </p>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                    <p class="text-yellow-700">
                        <i class="fab fa-google mr-2"></i>
                            <a href="{{ route('admin.google.auth') }}" class="underline">Conectar con Google Calendar</a>
                        para sincronizar citas automáticamente
                    </p>
                </div>
            @endif

            <!-- Lista de Citas -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                    Google Calendar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($citas as $cita)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $cita->cliente->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $cita->cliente->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $cita->servicio->nombre }}</div>
                                        <div class="text-sm text-gray-500">${{ number_format($cita->servicio->precio, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $cita->fecha_cita->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $cita->hora_cita }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $cita->empleado ? $cita->empleado->name : 'No asignado' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($cita->estado_cita == 'confirmada') bg-green-100 text-green-800
                                            @elseif($cita->estado_cita == 'pendiente') bg-yellow-100 text-yellow-800
                                            @elseif($cita->estado_cita == 'cancelada') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($cita->estado_cita) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cita->isSyncedWithGoogle())
                                            <span class="text-green-600" title="Sincronizado el {{ $cita->last_sync_at->format('d/m/Y H:i') }}">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @elseif($isConnected)
                                            <form action="{{ route('admin.citas.sync', $cita) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900" title="Sincronizar con Google Calendar">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400" title="Conectar Google Calendar para sincronizar">
                                                <i class="fas fa-times-circle"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.citas.show', $cita) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.citas.edit', $cita) }}" class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.citas.destroy', $cita) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta cita?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($citas->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <p class="text-gray-500 text-lg">No hay citas programadas</p>
                    <a href="{{ route('admin.citas.create') }}" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                        Crear primera cita
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>