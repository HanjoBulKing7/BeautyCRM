@extends('layouts.app')

@section('content')
<style>
  /* ✅ FORZAR: Tabla en desktop, Cards en móvil */
  @media (max-width: 767.98px) {
    .clientes-table { display: none !important; }
    .clientes-cards { display: block !important; }
  }
  @media (min-width: 768px) {
    .clientes-table { display: block !important; }
    .clientes-cards { display: none !important; }
  }
</style>

<div class="container mx-auto px-4 py-6">

  <!-- Encabezado -->
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
        <span class="bb-icon-pill">
          <!-- icon: users -->
          <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H2v-2a4 4 0 013-3.87m11-3.13a4 4 0 10-8 0 4 4 0 008 0zM20 8a4 4 0 00-6.2-3.33"/>
          </svg>
        </span>
        Clientes
      </h1>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.clientes.create') }}" class="bb-btn-gold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Cliente
      </a>
    </div>
  </div>

  <!-- ===================== -->
  <!-- ✅ TABLA (DESKTOP/TABLET) -->
  <!-- ===================== -->
  <div class="clientes-table">
    <div class="bb-glass-card">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bb-thead">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                Cliente
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                Email
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                Teléfono
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>

          <tbody>
            @forelse($clientes as $cliente)
              <tr class="bb-row">
                <td class="px-4 py-3">
                  <div class="flex items-start gap-3">
                    <span class="bb-icon-pill" style="width:34px;height:34px;border-radius:12px;">
                      <!-- icon: user -->
                      <svg class="w-4 h-4 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                    </span>

                    <div>
                      <div class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $cliente->nombre ?? $cliente->name ?? 'Cliente' }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        ID: #{{ $cliente->id ?? '-' }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <div class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $cliente->email ?? '—' }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="text-sm text-gray-700 dark:text-gray-200">
                    {{ $cliente->telefono ?? $cliente->phone ?? 'No especificado' }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Ver -->
                    <a href="{{ route('admin.clientes.show', $cliente->id) }}"
                       class="bb-action text-gray-700 dark:text-gray-200"
                       title="Ver">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </a>

                    <!-- Editar -->
                    <a href="{{ route('admin.clientes.edit', $cliente->id) }}"
                       class="bb-action bb-action-edit"
                       title="Editar">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                      </svg>
                    </a>

                    <!-- Eliminar -->
                    <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar cliente?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="bb-action bb-action-del"
                              title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                      <span class="text-2xl">👥</span>
                    </div>
                    <p class="font-semibold mt-3 text-gray-800 dark:text-white">No hay clientes registrados</p>
                    <p class="text-sm">Crea tu primer cliente para comenzar</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      @if(method_exists($clientes, 'hasPages') && $clientes->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          {{ $clientes->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ===================== -->
  <!-- ✅ MÓVIL: CARDS (con tus botones bb-action intactos) -->
  <!-- ===================== -->
  <div class="clientes-cards space-y-3">
    @forelse($clientes as $cliente)
      <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4 dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-xs text-gray-500 dark:text-gray-400">Cliente</div>

            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
              {{ $cliente->nombre ?? $cliente->name ?? 'Cliente' }}
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              ID: #{{ $cliente->id ?? '-' }}
            </div>

            <div class="text-xs text-gray-600 dark:text-gray-400 mt-2 space-y-1">
              <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-[11px] text-gray-400"></i>
                <span class="truncate">{{ $cliente->email ?? '—' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-phone text-[11px] text-gray-400"></i>
                <span>{{ $cliente->telefono ?? $cliente->phone ?? 'No especificado' }}</span>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-2">
              <!-- Ver -->
              <a href="{{ route('admin.clientes.show', $cliente->id) }}"
                 class="bb-action text-gray-700 dark:text-gray-200"
                 title="Ver">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </a>

              <!-- Editar -->
              <a href="{{ route('admin.clientes.edit', $cliente->id) }}"
                 class="bb-action bb-action-edit"
                 title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                </svg>
              </a>

              <!-- Eliminar -->
              <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST"
                    onsubmit="return confirm('¿Eliminar cliente?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bb-action bb-action-del"
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
        No hay clientes registrados.
      </div>
    @endforelse
  </div>

</div>
@endsection
