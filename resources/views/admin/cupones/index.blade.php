@extends('layouts.app')

@section('title', 'Cupones y Promociones')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-ticket-alt mr-3" style="color: rgba(201,162,74,.92)"></i>
                    Cupones y Promociones
                </h1>
                <p class="text-gray-600 mt-1">Gestiona todos tus cupones, códigos y promociones</p>
            </div>

            <a href="{{ route('admin.cupones.create') }}"
               class="px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition"
               style="background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                       border: 1px solid rgba(201,162,74,.35);
                       box-shadow: 0 10px 22px rgba(201,162,74,.18);
                       color: #111827;">
                <i class="fas fa-plus"></i>
                Nuevo Cupón
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
                <i class="fas fa-check-circle mr-2"></i> {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ $message }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6 shadow-sm">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" placeholder="Buscar por código o nombre..."
                       value="{{ request('search') }}"
                       class="border border-gray-300 rounded-lg px-4 py-2 transition
                              focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                
                <select name="estado" class="border border-gray-300 rounded-lg px-4 py-2 transition
                                          focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.28)]">
                    <option value="">-- Estado --</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-medium transition">
                    <i class="fas fa-filter mr-2"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @if ($cupones->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Código</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Descuento</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Vigencia</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Usos</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($cupones as $cupon)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.cupones.show', $cupon) }}"
                                           class="font-mono bg-gray-100 px-3 py-1 rounded text-sm font-medium hover:bg-gray-200 transition inline-block">
                                            {{ $cupon->codigo }}
                                        </a>
                                        @if ($cupon->aplica_cumpleaños)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-pink-100 text-pink-800">
                                                <i class="fas fa-birthday-cake mr-1"></i> Cumpleaños
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">{{ $cupon->nombre }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 font-semibold">
                                            @if ($cupon->tipo_descuento === 'porcentaje')
                                                {{ $cupon->valor_descuento }}%
                                            @else
                                                ${{ number_format((float)$cupon->valor_descuento, 2) }}
                                            @endif
                                        </div>
                                        @if ($cupon->descuento_maximo)
                                            <small class="text-gray-600">Max: ${{ number_format((float)$cupon->descuento_maximo, 2) }}</small>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if ($cupon->fecha_inicio || $cupon->fecha_fin)
                                            {{ optional($cupon->fecha_inicio)->format('d/m/Y') ?? 'Sin inicio' }} -
                                            {{ optional($cupon->fecha_fin)->format('d/m/Y') ?? 'Sin fin' }}
                                        @else
                                            <span class="text-gray-400">Permanente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <span class="font-semibold">{{ $cupon->usos_actuales }}</span>
                                            @if ($cupon->cantidad_usos)
                                                <span class="text-gray-600">/ {{ $cupon->cantidad_usos }}</span>
                                            @else
                                                <span class="text-gray-600">/ ∞</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                   {{ $cupon->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($cupon->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.cupones.show', $cupon) }}"
                                           class="inline-flex items-center px-3 py-2 rounded text-sm font-medium text-gray-600 hover:bg-gray-100 transition"
                                           title="Ver detalles">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        <a href="{{ route('admin.cupones.edit', $cupon) }}"
                                           class="inline-flex items-center px-3 py-2 rounded text-sm font-medium text-blue-600 hover:bg-blue-50 transition">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.cupones.destroy', $cupon) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar este cupón?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 rounded text-sm font-medium text-red-600 hover:bg-red-50 transition">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    {{ $cupones->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay cupones registrados.</p>
                    <a href="{{ route('admin.cupones.create') }}" class="mt-4 inline-block px-4 py-2 rounded-lg bg-[rgba(201,162,74,.12)] text-[rgba(201,162,74,.92)] font-medium hover:bg-[rgba(201,162,74,.20)] transition">
                        Crear el primer cupón
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
