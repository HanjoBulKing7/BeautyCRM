@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- ✅ Estilos locales (directo en este archivo) usando TU dorado --}}
    <style>
        :root{
            --bb-gold: rgba(201,162,74,.95);
            --bb-gold-soft: rgba(201,162,74,.14);
            --bb-gold-border: rgba(201,162,74,.22);
            --bb-ink: rgba(17,24,39,.92);
            --bb-muted: rgba(107,114,128,.92);
            --bb-border: rgba(17,24,39,.08);
            --bb-glass: rgba(255,255,255,.72);
            --bb-glass-2: rgba(255,255,255,.55);
        }

        .bb-glass-card{
            background: var(--bb-glass);
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border: 1px solid rgba(255,255,255,.65);
            box-shadow: 0 10px 26px rgba(17,24,39,.06);
            border-radius: 1rem;
        }

        .bb-icon-pill{
            width: 40px; height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.55);
            border: 1px solid rgba(201,162,74,.18);
            box-shadow: 0 10px 22px rgba(17,24,39,.06);
        }
        .bb-gold{ color: var(--bb-gold) !important; }

        .bb-btn-gold{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            padding:.6rem 1rem;
            border-radius: .95rem;
            font-weight: 800;
            color: #111827;
            background: linear-gradient(135deg, rgba(201,162,74,.95), rgba(231,215,161,.95));
            border: 1px solid rgba(201,162,74,.35);
            box-shadow: 0 12px 28px rgba(201,162,74,.18);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }
        .bb-btn-gold:hover{
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(17,24,39,.10);
            filter: brightness(1.02);
        }

        .bb-btn-ghost{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            padding:.6rem 1rem;
            border-radius: .95rem;
            font-weight: 700;
            color: rgba(17,24,39,.88);
            background: rgba(255,255,255,.60);
            border: 1px solid rgba(255,255,255,.65);
            box-shadow: 0 10px 22px rgba(17,24,39,.06);
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .bb-btn-ghost:hover{
            transform: translateY(-1px);
            background: rgba(255,255,255,.78);
            box-shadow: 0 16px 30px rgba(17,24,39,.08);
        }

        .bb-input{
            width: 100%;
            border-radius: .95rem;
            border: 1px solid rgba(17,24,39,.10);
            background: rgba(255,255,255,.70);
            padding: .65rem .85rem;
            outline: none;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .bb-input:focus{
            border-color: rgba(201,162,74,.28);
            box-shadow: 0 0 0 3px rgba(201,162,74,.18);
        }

        .bb-thead{
            background: rgba(255,255,255,.35);
            border-bottom: 1px solid var(--bb-border);
        }

        .bb-row{
            border-bottom: 1px solid rgba(17,24,39,.06);
            transition: background .2s ease;
        }

        /* ✅ Estados (pastel) */
        tr.bb-state-cancelada{
            background: rgba(254, 226, 226, .75); /* red-100-ish */
        }
        tr.bb-state-completada{
            background: rgba(220, 252, 231, .75); /* green-100-ish */
        }
        tr.bb-state-confirmada{
            background: rgba(254, 249, 195, .78); /* yellow-100-ish */
        }

        tr.bb-state-cancelada:hover{ background: rgba(254, 226, 226, .92); }
        tr.bb-state-completada:hover{ background: rgba(220, 252, 231, .92); }
        tr.bb-state-confirmada:hover{ background: rgba(254, 249, 195, .92); }

        /* Hover default para filas sin estado */
        tr.bb-row:hover{ background: rgba(255,255,255,.45); }

        .bb-pill{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            padding: .35rem .65rem;
            border-radius: .8rem;
            background: rgba(255,255,255,.55);
            border: 1px solid rgba(255,255,255,.60);
            font-weight: 700;
        }

        .bb-pill-gold{
            background: var(--bb-gold-soft);
            border-color: var(--bb-gold-border);
            color: rgba(17,24,39,.90);
        }

        .bb-action{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:.4rem;
            padding: .55rem .75rem;
            border-radius: .9rem;
            background: rgba(255,255,255,.50);
            border: 1px solid rgba(255,255,255,.55);
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
            white-space: nowrap;
        }
        .bb-action:hover{
            transform: translateY(-1px);
            background: rgba(255,255,255,.70);
            box-shadow: 0 12px 24px rgba(17,24,39,.08);
        }
        .bb-action-gold{ color: var(--bb-gold) !important; border-color: rgba(201,162,74,.18) !important; }
        .bb-action-ink{ color: rgba(17,24,39,.85) !important; }

        /* Dark mode */
        .dark-mode .bb-glass-card{
            background: rgba(17,24,39,.55);
            border-color: rgba(255,255,255,.10);
        }
        .dark-mode .bb-thead{
            background: rgba(17,24,39,.45);
            border-bottom-color: rgba(255,255,255,.10);
        }
        .dark-mode .bb-row{ border-bottom-color: rgba(255,255,255,.08); }
        .dark-mode .bb-input{
            background: rgba(17,24,39,.35);
            border-color: rgba(255,255,255,.10);
            color: rgba(249,250,251,.92);
        }
        .dark-mode .bb-input:focus{ box-shadow: 0 0 0 3px rgba(201,162,74,.16); }
        .dark-mode .text-gray-800{ color: rgba(249,250,251,.95) !important; }
        .dark-mode .text-gray-700{ color: rgba(229,231,235,.92) !important; }
        .dark-mode .text-gray-600{ color: rgba(209,213,219,.86) !important; }
        .dark-mode .text-gray-500{ color: rgba(156,163,175,.92) !important; }
        .dark-mode .text-gray-900{ color: rgba(249,250,251,.98) !important; }
    </style>

    {{-- ✅ Orden por fecha+hora (dentro de la página actual) --}}
    @php
        if (method_exists($citasCompletadas, 'setCollection') && method_exists($citasCompletadas, 'getCollection')) {
            $sorted = $citasCompletadas->getCollection()->sortBy(function($c) {
                $f = $c->fecha_cita ? \Carbon\Carbon::parse($c->fecha_cita)->format('Y-m-d') : '9999-12-31';
                $h = substr((string)($c->hora_cita ?? ''), 0, 5);
                return $f.' '.$h;
            })->values();
            $citasCompletadas->setCollection($sorted);
        }

        // ✅ Conteo de confirmadas (ideal: pásalo desde el controller para que sea global con paginación)
        $confirmadasLocal = 0;
        try {
            $confirmadasLocal = method_exists($citasCompletadas, 'getCollection')
                ? $citasCompletadas->getCollection()->where('estado_cita', 'confirmada')->count()
                : 0;
        } catch (\Throwable $e) {
            $confirmadasLocal = 0;
        }
        $pendientesCountView = $pendientesCount ?? $confirmadasLocal;
    @endphp

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <span class="bb-icon-pill">
                    <!-- icon: cash -->
                    <svg class="w-5 h-5 bb-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m2-8h-6a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V11a2 2 0 00-2-2z"/>
                    </svg>
                </span>
                Ventas (Citas)
            </h1>
        </div>
    </div>

    <!-- Filtros (auto-submit) -->
    <div class="bb-glass-card p-4 mb-6">
        <form id="filtroVentasForm" method="GET" action="{{ route('admin.ventas.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio ?? '' }}" class="bb-input">
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin ?? '' }}" class="bb-input">
            </div>

            <div class="flex items-end gap-2">
                {{-- ✅ Quitamos botón Filtrar (auto) --}}
                <a href="{{ route('admin.ventas.index') }}" class="bb-btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M20 8a8 8 0 00-14.828-2M4 16a8 8 0 0014.828 2"/>
                    </svg>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Resumen rápido -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bb-glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="bb-icon-pill" style="width:42px;height:42px;">
                    <span class="text-xl">💰</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ventas</p>
                    <p class="text-xl font-extrabold bb-gold">${{ number_format($totalVentas ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bb-glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="bb-icon-pill" style="width:42px;height:42px;">
                    <span class="text-xl">✅</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Citas Completadas</p>
                    <p class="text-xl font-extrabold text-gray-900">{{ $ventasCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- ✅ Reemplaza Ticket Promedio por Pendientes (Confirmadas) --}}
        <div class="bb-glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="bb-icon-pill" style="width:42px;height:42px;">
                    <span class="text-xl">🕒</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Citas Pendientes (Confirmadas)</p>
                    <p class="text-xl font-extrabold text-gray-900">{{ $pendientesCountView ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bb-glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bb-thead">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha Cita</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Venta</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($citasCompletadas as $cita)
                        @php
                            $estado = strtolower(trim((string)($cita->estado_cita ?? '')));
                            $rowStateClass = '';
                            if ($estado === 'cancelada') $rowStateClass = 'bb-state-cancelada';
                            elseif ($estado === 'completada') $rowStateClass = 'bb-state-completada';
                            elseif ($estado === 'confirmada') $rowStateClass = 'bb-state-confirmada';

                            $horaTxt = substr((string)($cita->hora_cita ?? ''), 0, 5);
                        @endphp

                        <tr class="bb-row {{ $rowStateClass }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">#{{ $cita->id_cita }}</span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $horaTxt }}</div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                {{-- ✅ FIX: Cliente normalmente es "nombre" --}}
                                <div class="text-sm font-semibold text-gray-900">{{ $cita->cliente->nombre ?? 'Cliente' }}</div>
                                @if($cita->cliente->email ?? false)
                                    <div class="text-xs text-gray-500">{{ $cita->cliente->email }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                @forelse($cita->servicios as $s)
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-sm text-gray-900 truncate">
                                            {{ $s->nombre_servicio }}
                                        </div>
                                        <div class="text-xs text-gray-500 whitespace-nowrap">
                                            ${{ number_format($s->pivot->precio_snapshot ?? $s->precio ?? 0, 2) }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">Sin servicios</div>
                                @endforelse
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ trim(($cita->empleado->nombre ?? '').' '.($cita->empleado->apellido ?? '')) ?: 'No asignado' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($cita->venta)
                                    <span class="bb-pill bb-pill-gold">
                                        ${{ number_format($cita->venta->total, 2) }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $cita->venta->forma_pago ?? 'efectivo' }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Sin venta registrada</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- ✅ Solo botón Ver Cita (sin Ver Venta) --}}
                                    <a href="{{ route('admin.citas.show', $cita->id_cita) }}"
                                       class="bb-action bb-action-ink"
                                       title="Ver Cita">
                                        {{-- ✅ Icono OJO --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Ver Cita
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <div class="text-gray-500">
                                    <div class="mx-auto bb-icon-pill" style="width:56px;height:56px;border-radius:18px;">
                                        <span class="text-2xl">📭</span>
                                    </div>
                                    <p class="font-semibold mt-3 text-gray-800">No hay citas</p>
                                    <p class="text-sm">Las citas aparecerán aquí cuando existan registros en el rango seleccionado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($citasCompletadas->hasPages())
            <div class="px-4 py-3 border-t" style="border-color: rgba(17,24,39,.08);">
                {{ $citasCompletadas->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ✅ Auto-submit del filtro al cambiar fechas --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filtroVentasForm');
    if (!form) return;

    const inicio = form.querySelector('input[name="fecha_inicio"]');
    const fin    = form.querySelector('input[name="fecha_fin"]');

    function autoSubmit() { form.submit(); }

    if (inicio) inicio.addEventListener('change', autoSubmit);
    if (fin)    fin.addEventListener('change', autoSubmit);
});
</script>
@endsection