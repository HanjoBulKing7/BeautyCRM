@php
  $mxn = fn($n) => '$' . number_format((float)$n, 2);

  $inicio = \Carbon\Carbon::parse(data_get($rango,'inicio'));
  $fin    = \Carbon\Carbon::parse(data_get($rango,'fin'));
  
  // Variables para compatibilidad de interfaz
  $semanaNumero = $inicio->weekOfYear;
  $anio         = $inicio->year;

  $ventas = data_get($stats, 'ventas', []);
  $citas  = data_get($stats, 'citas', []);
  $clientesNuevos = (int) data_get($stats, 'clientes.nuevos', 0);

  $metodos = collect(data_get($ventas, 'metodos_pago', []));
  $topServicios = collect(data_get($stats, 'servicios.top', []));
  $topEmpleados = collect(data_get($stats, 'empleados.top', []));
  $productosStats = data_get($stats, 'productos_stats', []);
  $topProductos = collect(data_get($stats, 'productos_stats.top', []));

  $resumenChart = data_get($ventas, 'resumen_chart', null);
  $resumenPagos = data_get($ventas, 'resumen_pagos', null);
@endphp

<h4 class="font-semibold text-lg mb-2 flex items-center text-gray-800 dark:text-gray-100">
    <i class="fas fa-calendar-week text-blue-500 mr-2"></i>
    Reporte Semanal
</h4>

<div class="text-sm text-gray-500 mb-4 flex items-center dark:text-gray-400">
    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
    <span>
        <strong>Semana {{ $semanaNumero }} ·</strong>
        {{ $inicio->translatedFormat('d M Y') }} — {{ $fin->translatedFormat('d M Y') }}
    </span>
</div>

{{-- Cards Superiores --}}
@include('admin.reportes.partials.cards')

{{-- Productos vendidos --}}
<div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700 mt-4">
  <h5 class="text-sm font-semibold text-gray-800 flex items-center mb-3 dark:text-gray-200">
      <i class="fas fa-boxes-stacked text-purple-500 mr-2"></i>
      Ventas de productos
  </h5>

  <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300 mb-3">
    <div><strong>Total vendido:</strong> {{ $mxn(data_get($productosStats, 'total_monto', 0)) }}</div>
    <div><strong>Ventas:</strong> {{ (int) data_get($productosStats, 'total_cantidad', 0) }}</div>
  </div>

  @if($topProductos->isEmpty())
    <div class="text-center py-3 text-gray-400 dark:text-gray-500 text-xs">Sin productos vendidos en este rango.</div>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700/50">
            <th class="pb-2 font-medium">Producto</th>
            <th class="pb-2 font-medium">Cantidad</th>
            <th class="pb-2 font-medium">Ingresos</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
          @foreach($topProductos as $p)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
              <td class="py-2 text-gray-600 dark:text-gray-300">{{ $p->nombre ?? '-' }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-300">{{ (int) ($p->total_vendido ?? 0) }}</td>
              <td class="py-2 font-semibold text-purple-600 dark:text-purple-400">{{ $mxn($p->ingresos ?? 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

{{-- ===========================
     Listas Top
=========================== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
  
  {{-- Métodos de Pago --}}
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h5 class="text-sm font-semibold text-gray-800 flex items-center mb-3 dark:text-gray-200">
        <i class="fas fa-wallet text-blue-500 mr-2"></i>
        Métodos de pago
    </h5>
    @if($metodos->isEmpty())
      <div class="text-center py-4 text-gray-400 dark:text-gray-500 text-xs">Sin datos.</div>
    @else
      <div class="space-y-2 mt-2">
        @foreach($metodos as $m)
          <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
            <span class="text-gray-600 dark:text-gray-300">{{ $m->metodo_label ?? $m->metodo ?? '-' }}</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn($m->monto ?? 0) }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Top Servicios --}}
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h5 class="text-sm font-semibold text-gray-800 flex items-center mb-3 dark:text-gray-200">
        <i class="fas fa-star text-yellow-500 mr-2"></i>
        Top servicios
    </h5>
    @if($topServicios->isEmpty())
      <div class="text-center py-4 text-gray-400 dark:text-gray-500 text-xs">Sin datos.</div>
    @else
      <div class="space-y-2 mt-2">
        @foreach($topServicios as $s)
          <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
            <span class="text-gray-600 dark:text-gray-300 truncate pr-2">{{ $s->servicio ?? '-' }}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 font-semibold">
                {{ (int)($s->veces ?? 0) }} citas
            </span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Top Empleados --}}
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h5 class="text-sm font-semibold text-gray-800 flex items-center mb-3 dark:text-gray-200">
        <i class="fas fa-user-tie text-green-500 mr-2"></i>
        Top empleados
    </h5>
    @if($topEmpleados->isEmpty())
      <div class="text-center py-4 text-gray-400 dark:text-gray-500 text-xs">Sin datos.</div>
    @else
      <div class="space-y-2 mt-2">
        @foreach($topEmpleados as $e)
          <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
            <span class="text-gray-600 dark:text-gray-300 truncate pr-2">{{ $e->empleado ?? '(Sin nombre)' }}</span>
            <span class="font-semibold text-green-600 dark:text-green-400">{{ $mxn($e->ingresos ?? 0) }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

{{-- ===========================
     Gráfica + Resumen
=========================== --}}
@if($resumenChart && $resumenPagos)
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 mb-8">

    {{-- Gráfica (Ocupa 2/3 en escritorio) --}}
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
      <div class="flex items-center justify-between mb-3">
          <div class="flex flex-col">
              <h5 class="text-sm font-semibold text-gray-800 flex items-center dark:text-gray-200">
                  <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                  Ventas por método de pago
              </h5>
              <span class="text-xs text-gray-400 dark:text-gray-500">
                  Resumen de la semana seleccionada
              </span>
          </div>
      </div>

      <div class="h-64 mt-4">
        <canvas id="resumenPagosChart-semanal"
                data-labels='@json(data_get($resumenChart,"labels",[]))'
                data-values='@json(data_get($resumenChart,"values",[]))'></canvas>
      </div>

      @php $mixto = (float) data_get($resumenPagos,'mixto.monto',0); @endphp
      @if($mixto > 0)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 italic">
          * Nota: Los pagos en formato "Mixto" se suman al total general, pero no se desglosan como barra individual en la gráfica.
        </p>
      @endif
    </div>

    {{-- Resumen Total (Ocupa 1/3 en escritorio) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
      <h5 class="text-sm font-semibold text-gray-800 flex items-center mb-4 dark:text-gray-200">
          <i class="fas fa-receipt text-blue-500 mr-2"></i>
          Resumen General
      </h5>

      <div class="space-y-4 text-sm mt-2">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 pb-2">
          <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Efectivo</span>
          <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn(data_get($resumenPagos,'efectivo.monto',0)) }}</span>
        </div>

        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 pb-2">
          <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Transferencia</span>
          <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn(data_get($resumenPagos,'transferencia.monto',0)) }}</span>
        </div>

        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 pb-2">
          <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Tarjeta</span>
          <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn(data_get($resumenPagos,'tarjeta.monto',0)) }}</span>
        </div>

        @if((float)data_get($resumenPagos,'mixto.monto',0) > 0)
          <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 pb-2">
            <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Mixto</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn(data_get($resumenPagos,'mixto.monto',0)) }}</span>
          </div>
        @endif

        @if((float)data_get($resumenPagos,'otros.monto',0) > 0)
          <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 pb-2">
            <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Otros</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $mxn(data_get($resumenPagos,'otros.monto',0)) }}</span>
          </div>
        @endif

        <div class="flex items-center justify-between pt-2">
          <span class="text-gray-800 dark:text-gray-200 font-bold uppercase text-xs">TOTAL INGRESOS</span>
          <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $mxn(data_get($resumenPagos,'total.monto',0)) }}</span>
        </div>
      </div>
    </div>

  </div>
@endif

{{-- Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // =======================
  // CHART.JS: Gráfica
  // =======================
  const elChart = document.getElementById('resumenPagosChart-semanal');
  if (elChart && typeof Chart !== 'undefined') {
    const labels = JSON.parse(elChart.dataset.labels || '[]');
    const values = JSON.parse(elChart.dataset.values || '[]');

    if (elChart._chartInstance) elChart._chartInstance.destroy();

    elChart._chartInstance = new Chart(elChart, {
      type: 'bar',
      data: { 
          labels, 
          datasets: [{ 
              label: 'Monto', 
              data: values,
              backgroundColor: '#3b82f6', // blue-500
              borderRadius: 6,
              maxBarThickness: 50
          }] 
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
            y: { 
                beginAtZero: true,
                grid: {
                    color: document.documentElement.classList.contains('dark') ? '#374151' : '#f3f4f6'
                }
            },
            x: {
                grid: { display: false }
            }
        }
      }
    });
  }
});
</script>
@endpush