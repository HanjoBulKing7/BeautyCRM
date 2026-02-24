@extends('layouts.website')

@section('title', 'Mis Reservas')
@section('page-title', 'Mis Reservas')

@push('styles')
<style>
  :root{
    --bb-gold: #C9A24A;
    --bb-gold-2: #E7D7A1;
    --bb-ink: #111827;
  }

  /* Scope total */
  #bb-misreservas .bb-card{
    border: 1px solid rgba(17,24,39,.08);
    border-radius: 18px;
    background: rgba(255,255,255,.92);
    box-shadow: 0 18px 40px rgba(0,0,0,.06);
  }

  #bb-misreservas .bb-pill{
    border: 1px solid rgba(201,162,74,.30);
    background: rgba(201,162,74,.10);
    color: rgba(26,26,26,.86);
  }

  #bb-misreservas .bb-btn-gold{
    background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
    border: 1px solid rgba(201,162,74,.35);
    box-shadow: 0 10px 22px rgba(201,162,74,.16);
    color: #111827;
  }
  #bb-misreservas .bb-btn-gold:hover{
    box-shadow: 0 16px 30px rgba(201,162,74,.20);
  }

  #bb-misreservas .bb-btn-soft{
    border: 1px solid rgba(17,24,39,.10);
    background: rgba(243,244,246,.9);
    color: rgba(17,24,39,.82);
  }

  #bb-misreservas .bb-tab{
    border: 1px solid rgba(17,24,39,.10);
    background: rgba(255,255,255,.85);
    border-radius: 999px;
    padding: .55rem .95rem;
    font-weight: 800;
    letter-spacing: .02em;
    color: rgba(17,24,39,.72);
  }
  #bb-misreservas .bb-tab.is-active{
    border-color: rgba(201,162,74,.45);
    background: rgba(201,162,74,.12);
    color: rgba(17,24,39,.88);
  }

  #bb-misreservas .bb-status{
    border-radius: 999px;
    padding: .35rem .7rem;
    font-weight: 900;
    font-size: .75rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    border: 1px solid rgba(17,24,39,.10);
    background: rgba(243,244,246,.9);
    color: rgba(17,24,39,.70);
  }
  #bb-misreservas .bb-status.is-confirmada{ border-color: rgba(16,185,129,.35); background: rgba(16,185,129,.10); color: rgba(5,150,105,.95); }
  #bb-misreservas .bb-status.is-pendiente{ border-color: rgba(201,162,74,.40); background: rgba(201,162,74,.12); color: rgba(184,134,11,.95); }
  #bb-misreservas .bb-status.is-cancelada{ border-color: rgba(239,68,68,.35); background: rgba(239,68,68,.08); color: rgba(220,38,38,.95); }
  #bb-misreservas .bb-status.is-completada{ border-color: rgba(59,130,246,.35); background: rgba(59,130,246,.08); color: rgba(37,99,235,.95); }

  #bb-misreservas .bb-glow{
    background:
      radial-gradient(1000px 420px at 10% -10%, rgba(201,162,74,.16), transparent 55%),
      radial-gradient(800px 380px at 90% 0%, rgba(231,215,161,.22), transparent 55%);
  }
  #bb-misreservas{ padding-top: 90px; }
@media (min-width: 768px){
  #bb-misreservas{ padding-top: 104px; }
}

</style>
@endpush

@section('content')
@include('beauty.partials.whatsApp-icon')
  @include('beauty.partials.header')
@php
  use Carbon\Carbon;
  use Carbon\CarbonInterface;

  $citas = $citas ?? collect();
  $now = now();

  $parsed = $citas->map(function($c) use ($now){
    $dt = null;

    try {
      $dateRaw = $c->fecha_cita ?? null;
      $timeRaw = $c->hora_cita ?? null;

      // ✅ Normaliza FECHA
      $dateStr = $dateRaw instanceof CarbonInterface
        ? $dateRaw->format('Y-m-d')
        : (is_string($dateRaw) ? trim($dateRaw) : null);

      // ✅ Normaliza HORA
      $timeStr = $timeRaw instanceof CarbonInterface
        ? $timeRaw->format('H:i:s')
        : (is_string($timeRaw) ? trim($timeRaw) : null);

      if ($dateStr && $timeStr) {
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', "{$dateStr} {$timeStr}");
      } elseif ($dateStr) {
        $dt = Carbon::createFromFormat('Y-m-d', $dateStr)->startOfDay();
      }
    } catch (\Throwable $e) {
      $dt = null;
    }

    $isUpcoming = $dt ? $dt->gte($now) : false;

    return (object)[
      'cita' => $c,
      'dt' => $dt,
      'isUpcoming' => $isUpcoming,
    ];
  });

  $upcoming = $parsed->where('isUpcoming', true)->values();
  $past     = $parsed->where('isUpcoming', false)->values();

  // ✅ FALTABAN ESTAS:
  $totalUpcoming = $upcoming->count();
  $totalPast     = $past->count();

  $next = $upcoming
    ->sortBy(fn($x) => $x->dt?->timestamp ?? PHP_INT_MAX)
    ->first();
@endphp



<div id="bb-misreservas" class="bb-glow">
  <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    {{-- Header --}}
    <div class="bb-card p-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">
            Mis Reservas
          </h1>
          <p class="text-sm text-gray-600 mt-1">
            Consulta tus próximas citas y tu historial. Cambia el filtro para encontrar rápido.
          </p>
        </div>

        <div class="flex flex-wrap gap-2">
          <span class="bb-pill inline-flex items-center px-3 py-2 rounded-full text-sm font-bold">
            <i class="fas fa-calendar-check mr-2" style="color: rgba(201,162,74,.95)"></i>
            Próximas: {{ $totalUpcoming }}
          </span>
          <span class="bb-pill inline-flex items-center px-3 py-2 rounded-full text-sm font-bold">
            <i class="fas fa-clock mr-2" style="color: rgba(201,162,74,.95)"></i>
            Historial: {{ $totalPast }}
          </span>
          @if($next && $next->dt)
            <span class="bb-pill inline-flex items-center px-3 py-2 rounded-full text-sm font-bold">
              <i class="fas fa-star mr-2" style="color: rgba(201,162,74,.95)"></i>
              Próxima: {{ $next->dt->translatedFormat('d M, g:i A') }}
            </span>
          @endif
        </div>
      </div>
    </div>

    {{-- Filtros (GET) --}}
    <div class="bb-card p-5">
      <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-4">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Buscar</label>
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Ej. faciales, pestañas, maquillaje…"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.25)]"
          >
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Estado</label>
          <select
            name="estado"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.25)]"
          >
            <option value="">Todos</option>
            @foreach(['pendiente','confirmada','cancelada','completada'] as $e)
              <option value="{{ $e }}" @selected(request('estado')===$e)>{{ ucfirst($e) }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Desde</label>
          <input
            type="date"
            name="from"
            value="{{ request('from') }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.25)]"
          >
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Hasta</label>
          <input
            type="date"
            name="to"
            value="{{ request('to') }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,162,74,.25)]"
          >
        </div>

        <div class="md:col-span-1 flex gap-2">
          <button type="submit" class="w-full px-4 py-3 rounded-xl font-extrabold bb-btn-gold">
            <i class="fas fa-filter mr-2"></i> Filtrar
          </button>
        </div>
      </form>
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2">
      <button type="button" class="bb-tab is-active" data-tab="upcoming">
        Próximas ({{ $upcoming->count() }})
      </button>
      <button type="button" class="bb-tab" data-tab="past">
        Historial ({{ $past->count() }})
      </button>
      <button type="button" class="bb-tab" data-tab="all">
        Todas ({{ $parsed->count() }})
      </button>
    </div>

    {{-- Listado --}}
    <div id="bbListWrap" class="space-y-4">

      {{-- Empty state --}}
      @if($parsed->isEmpty())
        <div class="bb-card p-10 text-center">
          <div class="mx-auto w-14 h-14 rounded-2xl grid place-items-center bb-pill">
            <i class="fas fa-calendar-times text-xl" style="color: rgba(201,162,74,.95)"></i>
          </div>
          <h3 class="text-lg font-black text-gray-900 mt-4">Aún no tienes reservas</h3>
          <p class="text-sm text-gray-600 mt-1">Cuando agendes una cita, aparecerá aquí con todos los detalles.</p>
          <a href="{{ url('/agendar-cita') }}"
             class="inline-flex items-center justify-center mt-5 px-6 py-3 rounded-xl font-extrabold bb-btn-gold">
            <i class="fas fa-plus mr-2"></i> Agendar ahora
          </a>
        </div>
      @endif

      @foreach($parsed as $item)
        @php
          $c = $item->cita;
          $dt = $item->dt;

          $estado = $c->estado_cita ?? 'pendiente';
          $statusClass = "is-{$estado}";

          // filtros server-side (si los usas)
          $q = trim((string)request('q'));
          $estadoReq = request('estado');
          $from = request('from') ? Carbon::parse(request('from'))->startOfDay() : null;
          $to   = request('to') ? Carbon::parse(request('to'))->endOfDay() : null;

          // texto de búsqueda: servicios + estado
          $svcNames = collect($c->servicios ?? [])->map(fn($s) => $s->nombre_servicio ?? $s->nombre ?? '')->implode(' ');
          $hayQ = $q ? (str_contains(mb_strtolower($svcNames), mb_strtolower($q)) || str_contains(mb_strtolower($estado), mb_strtolower($q))) : true;

          $hayEstado = $estadoReq ? ($estado === $estadoReq) : true;

          $hayRango = true;
          if ($dt && ($from || $to)) {
            if ($from && $dt->lt($from)) $hayRango = false;
            if ($to && $dt->gt($to)) $hayRango = false;
          }

          $visibleByServerFilters = $hayQ && $hayEstado && $hayRango;

          // totales por cita
          $totalCita = collect($c->servicios ?? [])->sum(function($s){
            $p = $s->pivot->precio_snapshot ?? $s->precio ?? 0;
            return (float)$p;
          });
          $descuento = (float)($c->descuento ?? 0);
          $totalPagar = max(0, $totalCita - $descuento);

          $durTotal = collect($c->servicios ?? [])->sum(function($s){
            $d = $s->pivot->duracion_snapshot ?? $s->duracion_minutos ?? 0;
            return (int)$d;
          });

          $dataTab = $item->isUpcoming ? 'upcoming' : 'past';
        @endphp

        <article
          class="bb-card p-5"
          data-tab="{{ $dataTab }}"
          style="{{ $visibleByServerFilters ? '' : 'display:none;' }}"
        >
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            {{-- Fecha / Estado --}}
            <div class="flex items-start gap-3">
              <div class="w-14 h-14 rounded-2xl grid place-items-center bb-pill">
                <i class="fas fa-calendar-day text-xl" style="color: rgba(201,162,74,.95)"></i>
              </div>
              <div>
                <div class="flex flex-wrap items-center gap-2">
                  <span class="bb-status {{ $statusClass }}">{{ $estado }}</span>
                  @if($dt)
                    <span class="text-sm font-extrabold text-gray-900">
                      {{ $dt->translatedFormat('d M Y') }} · {{ $dt->format('g:i A') }}
                    </span>
                  @else
                    <span class="text-sm font-extrabold text-gray-900">
                      Fecha pendiente
                    </span>
                  @endif
                </div>

                <div class="text-sm text-gray-600 mt-1">
                  <span class="font-semibold">Duración:</span> {{ $durTotal }} min
                  <span class="mx-2 text-gray-300">•</span>
                  <span class="font-semibold">Total:</span> ${{ number_format($totalPagar, 2) }} MXN
                </div>

                @if(!empty($c->empleado?->nombre))
                  <div class="text-sm text-gray-600 mt-1">
                    <span class="font-semibold">Atiende:</span> {{ $c->empleado->nombre }}
                  </div>
                @endif
              </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-wrap gap-2 md:justify-end">
              <button type="button"
                      class="px-4 py-3 rounded-xl font-extrabold bb-btn-soft"
                      data-toggle="details">
                <i class="fas fa-eye mr-2"></i> Detalles
              </button>

              @php
                $canCancel = in_array($estado, ['pendiente','confirmada'], true) && $dt && $dt->gte(now());
              @endphp

              @if($canCancel)
                {{-- Ajusta la ruta si ya la tienes (ej: misreservas.cancel) --}}
                <form method="POST" action="{{ route('misreservas.cancel', $c->id_cita ?? $c->id ?? 0) ?? '#' }}"
                      onsubmit="return confirm('¿Cancelar esta reserva?');">
                  @csrf
                  <button type="submit" class="px-4 py-3 rounded-xl font-extrabold bb-btn-soft">
                    <i class="fas fa-times mr-2"></i> Cancelar
                  </button>
                </form>
              @endif

              {{-- opcional: re-agendar --}}
              {{-- <a href="#" class="px-4 py-3 rounded-xl font-extrabold bb-btn-gold">Re-agendar</a> --}}
            </div>
          </div>

          {{-- Detalles (toggle) --}}
          <div class="mt-4 hidden" data-details>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
              <div class="md:col-span-8">
                <div class="text-xs font-black tracking-wider uppercase text-gray-600 mb-2">
                  Servicios
                </div>

                <div class="space-y-2">
                  @foreach($c->servicios ?? [] as $s)
                    @php
                      $name = $s->nombre_servicio ?? $s->nombre ?? 'Servicio';
                      $p = $s->pivot->precio_snapshot ?? $s->precio ?? 0;
                      $d = $s->pivot->duracion_snapshot ?? $s->duracion_minutos ?? 0;
                    @endphp

                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 bg-white">
                      <div class="font-bold text-gray-800">
                        {{ $name }}
                        <span class="text-xs text-gray-500 font-semibold ml-2">{{ (int)$d }} min</span>
                      </div>
                      <div class="font-black text-gray-900">
                        ${{ number_format((float)$p, 2) }}
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="md:col-span-4">
                <div class="text-xs font-black tracking-wider uppercase text-gray-600 mb-2">
                  Resumen
                </div>

                <div class="border border-gray-100 rounded-xl p-4 bg-white space-y-2">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 font-semibold">Subtotal</span>
                    <span class="text-gray-900 font-black">${{ number_format($totalCita, 2) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 font-semibold">Descuento</span>
                    <span class="text-gray-900 font-black">-${{ number_format($descuento, 2) }}</span>
                  </div>
                  <div class="h-px bg-gray-100 my-2"></div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700 font-extrabold">Total a pagar</span>
                    <span class="text-gray-900 font-black">${{ number_format($totalPagar, 2) }}</span>
                  </div>

                  @if(!empty($c->observaciones))
                    <div class="mt-3">
                      <div class="text-xs font-black tracking-wider uppercase text-gray-600 mb-1">Notas</div>
                      <div class="text-sm text-gray-700">{{ $c->observaciones }}</div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Tabs + toggle detalles
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = [...document.querySelectorAll('#bb-misreservas [data-tab]')];
    const tabBtns = [...document.querySelectorAll('#bb-misreservas .bb-tab')];

    function setActiveTab(tab){
      tabBtns.forEach(b => b.classList.toggle('is-active', b.dataset.tab === tab));
      document.querySelectorAll('#bbListWrap > article.bb-card').forEach(card => {
        const t = card.getAttribute('data-tab');
        card.style.display = (tab === 'all' || t === tab) ? '' : 'none';
      });
    }

    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
    });

    document.querySelectorAll('#bbListWrap [data-toggle="details"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const card = btn.closest('article');
        const details = card?.querySelector('[data-details]');
        if (!details) return;
        details.classList.toggle('hidden');
      });
    });

    // default
    setActiveTab('upcoming');
  });
</script>
@endpush
