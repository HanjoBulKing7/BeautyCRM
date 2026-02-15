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
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <span class="bb-icon-pill">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19.071 4.929a10 10 0 11-14.142 0 10 10 0 0114.142 0z"/>
          </svg>
        </span>
        Clientes
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">Gestiona tus clientes registrados</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.clientes.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                border border-[rgba(201,162,74,.35)]
                bg-[rgba(201,162,74,.12)] hover:bg-[rgba(201,162,74,.18)]
                text-gray-900 font-semibold shadow-sm hover:shadow transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Cliente
      </a>
    </div>
  </div>

  <!-- ✅ TABLA (DESKTOP) -->
  <div class="clientes-table">
    <div class="bb-card">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-4 py-3">Cliente</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Teléfono</th>
              <th class="px-4 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clientes as $cliente)
              @php $cid = $cliente->id; @endphp

              <tr class="bb-row">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <span class="bb-icon-pill flex items-center justify-center"
                          style="width:34px;height:34px;border-radius:12px;">
                      <i class="fas fa-user text-sm leading-none relative -top-px"></i>
                    </span>

                    <div class="min-w-0">
                      <div class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $cliente->nombre ?? $cliente->name ?? 'Cliente' }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        ID: #{{ $cid ?? '-' }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <span class="text-gray-700 dark:text-gray-200">
                    {{ $cliente->email ?? '—' }}
                  </span>
                </td>

                <td class="px-4 py-3">
                  <span class="text-gray-700 dark:text-gray-200">
                    {{ $cliente->telefono ?? $cliente->phone ?? 'No especificado' }}
                  </span>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">

                    <a href="{{ route('admin.clientes.show', $cid) }}"
                      class="bb-action inline-flex items-center justify-center leading-none"
                      title="Ver">
                      <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </a>

                    <a href="{{ route('admin.clientes.edit', $cid) }}"
                      class="bb-action bb-action-edit inline-flex items-center justify-center leading-none"
                      title="Editar">
                      <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                      </svg>
                    </a>

                    <form action="{{ route('admin.clientes.destroy', $cid) }}" method="POST" class="m-0 p-0 flex"
                          onsubmit="return confirm('¿Eliminar cliente?');">
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
                <td colspan="4" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                  No hay clientes registrados.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>

      @if(method_exists($clientes, 'hasPages') && $clientes->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          {{ $clientes->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ✅ CARDS (MÓVIL) -->
  <div class="clientes-cards space-y-3">
    @forelse($clientes as $cliente)
      @php $cid = $cliente->id; @endphp

      <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4 dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-xs text-gray-500 dark:text-gray-400">Cliente</div>

            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
              {{ $cliente->nombre ?? $cliente->name ?? 'Cliente' }}
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              ID: #{{ $cid ?? '-' }}
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
              <a href="{{ route('admin.clientes.show', $cid) }}"
                 class="inline-flex items-center justify-center leading-none bb-action text-gray-700 dark:text-gray-200"
                 title="Ver">
                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </a>

              <!-- Editar -->
              <a href="{{ route('admin.clientes.edit', $cid) }}"
                 class="inline-flex items-center justify-center leading-none bb-action bb-action-edit"
                 title="Editar">
                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l9.586-9.586z"/>
                </svg>
              </a>

              <!-- Eliminar -->
              <form action="{{ route('admin.clientes.destroy', $cid) }}" method="POST" class="m-0 p-0 flex"
                    onsubmit="return confirm('¿Eliminar cliente?');">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="inline-flex items-center justify-center leading-none bb-action bb-action-del"
                        title="Eliminar">
                  <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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