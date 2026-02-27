@extends('layouts.app')

@section('page-title', 'Gestión de Empleados')
@section('title', 'Empleados - Salón de Belleza')

@section('content')
<style>
  /* ✅ FORZAR: Tabla en desktop, Cards en móvil */
  @media (max-width: 767.98px) {
    .empleados-table { display: none !important; }
    .empleados-cards { display: block !important; }
  }
  @media (min-width: 768px) {
    .empleados-table { display: block !important; }
    .empleados-cards { display: none !important; }
  }
</style>

<div class="container mx-auto px-4 py-6">

  <!-- Encabezado -->
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
        <span class="bb-icon-pill">
          <!-- icon: briefcase -->
          <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V6a2 2 0 012-2h4a2 2 0 012 2v1m-9 0h10a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zm0 6h10"/>
          </svg>
        </span>
        Empleados
      </h1>
    </div>

    <div class="flex items-center gap-2">
      <!-- ✅ SIN MODAL -->
      <a href="{{ route('admin.empleados.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                border border-[rgba(201,162,74,.35)]
                bg-[rgba(201,162,74,.12)] hover:bg-[rgba(201,162,74,.18)]
                text-gray-900 font-semibold shadow-sm hover:shadow transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Empleado
      </a>
    </div>
  </div>

  <!-- ===================== -->
  <!-- ✅ TABLA (DESKTOP/TABLET) -->
  <!-- ===================== -->
  <div class="empleados-table hidden md:block">
    <div class="bb-glass-card">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bb-thead bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Empleado</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Email</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($empleados as $empleado)
              <tr class="bb-row">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <span class="bb-icon-pill flex items-center justify-center" style="width:34px;height:34px;border-radius:12px;">
                      <svg class="w-4 h-4 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $empleado->nombre ?? $empleado->name ?? 'Empleado' }}
                      </div>
                      <div class="text-xs text-gray-500">ID: #{{ $empleado->id ?? '-' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $empleado->email ?? '—' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $empleado->telefono ?? 'No especificado' }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.empleados.edit', $empleado->id) }}"
                      class="bb-action bb-action-edit inline-flex items-center justify-center leading-none"
                      title="Editar">
                        <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="m-0 p-0 inline-flex items-center"
                          onsubmit="return confirm('¿Eliminar empleado?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="bb-action bb-action-del inline-flex items-center justify-center leading-none"
                              title="Eliminar">
                        <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-10 text-center">
                  <div class="text-gray-500 dark:text-gray-300">
                    <div class="mx-auto bb-icon-pill" style="width:56px;height:56px;border-radius:18px;">
                      <span class="text-2xl">✨</span>
                    </div>
                    <p class="font-semibold mt-3 text-gray-800 dark:text-white">No hay empleados registrados</p>
                    <p class="text-sm">Crea tu primer empleado para comenzar</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      @if(method_exists($empleados, 'hasPages') && $empleados->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          {{ $empleados->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ===================== -->
  <!-- ✅ MÓVIL: CARDS -->
  <!-- ===================== -->
  <div class="empleados-cards space-y-3 md:hidden">
    @forelse($empleados as $empleado)
      <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4 dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-xs text-gray-500 dark:text-gray-400">Empleado</div>

            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
              {{ $empleado->nombre ?? $empleado->name ?? 'Empleado' }}
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              ID: #{{ $empleado->id ?? '-' }}
            </div>

            <div class="text-xs text-gray-600 dark:text-gray-400 mt-2 space-y-1">
              <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-[11px] text-gray-400"></i>
                <span class="truncate">{{ $empleado->email ?? '—' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-phone text-[11px] text-gray-400"></i>
                <span>{{ $empleado->telefono ?? 'No especificado' }}</span>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-2">

              <!-- ✅ Editar (SIN MODAL) -->
              <a href="{{ route('admin.empleados.edit', $empleado->id) }}"
                class="bb-action bb-action-icon bb-action-ink"
                title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                </svg>
              </a>

              <!-- Eliminar -->
              <form class="inline-flex"
                    action="{{ route('admin.empleados.destroy', $empleado->id) }}"
                    method="POST"
                    onsubmit="return confirm('¿Eliminar empleado?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bb-action bb-action-icon bb-action-ink"
                        title="Eliminar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
                  </svg>
                </button>
              </form>

            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="text-center text-gray-500 text-sm dark:text-gray-400 mt-4">
        No hay empleados registrados.
      </div>
    @endforelse
  </div>

</div>
@endsection