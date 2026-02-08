@php
  $layout = view()->exists('layouts.admin') ? 'layouts.admin' : 'layouts.app';

  $citas = $citas ?? collect();

  $money = fn($v) => is_numeric($v) ? '$'.number_format((float)$v, 2, '.', ',') : '—';

  $badge = function ($value) {
    $v = strtolower((string)($value ?? ''));
    $cls = 'bb-badge warn';
    if (in_array($v, ['pagado','paid','succeeded','confirmado','confirmed'])) $cls = 'bb-badge ok';
    if (in_array($v, ['cancelado','canceled','fallido','failed'])) $cls = 'bb-badge bad';
    return [$cls, $value ?: '—'];
  };

  $has = fn($name) => \Illuminate\Support\Facades\Route::has($name);

  $urlConfirmarCita = fn($id) =>
    $has('admin.citas.confirmar')
      ? route('admin.citas.confirmar', $id)
      : url('/admin/citas/'.$id.'/confirmar');

  $urlMarcarPagado = fn($id) =>
    $has('admin.pagos.marcarPagado')
      ? route('admin.pagos.marcarPagado', $id)
      : url('/admin/pagos/'.$id.'/marcar-pagado');

  $urlShowPago = fn($id) =>
    $has('admin.pagos.show')
      ? route('admin.pagos.show', $id)
      : url('/admin/pagos/'.$id);
@endphp

@extends($layout)

@section('content')
@once
<style>
  :root{
    --bb-gold: rgba(201,162,74,.95);
    --bb-ink: rgba(17,24,39,.92);
    --bb-muted: rgba(107,114,128,.92);
    --bb-glass: rgba(255,255,255,.72);
  }
  .bb-wrap{ max-width: 1200px; margin: 0 auto; padding: 22px 16px; }
  .bb-head{ display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom: 14px; }
  .bb-h1{ font-size: 22px; font-weight: 900; color: var(--bb-ink); margin:0; }
  .bb-sub{ color: var(--bb-muted); margin:6px 0 0; }
  .bb-card{
    background: var(--bb-glass);
    border: 1px solid rgba(255,255,255,.65);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 10px 26px rgba(17,24,39,.06);
    border-radius: 18px;
    padding: 16px;
  }
  .bb-table{ width:100%; border-collapse: collapse; overflow:hidden; border-radius: 14px; }
  .bb-table th, .bb-table td{ padding: 12px 10px; border-bottom: 1px solid rgba(17,24,39,.08); text-align:left; vertical-align: top; }
  .bb-table th{ font-size: 12px; color: var(--bb-muted); font-weight: 900; background: rgba(255,255,255,.65); }
  .bb-table td{ color: var(--bb-ink); font-weight: 700; background: rgba(255,255,255,.85); }
  .bb-badge{
    display:inline-flex; align-items:center;
    padding: 6px 10px; border-radius: 999px;
    border: 1px solid rgba(17,24,39,.10);
    font-weight: 900; font-size: 12px;
    background: white;
  }
  .bb-badge.ok{ border-color: rgba(16,185,129,.25); background: rgba(16,185,129,.08); color: rgba(5, 120, 87, 1); }
  .bb-badge.warn{ border-color: rgba(245,158,11,.28); background: rgba(245,158,11,.10); color: rgba(146, 64, 14, 1); }
  .bb-badge.bad{ border-color: rgba(239,68,68,.25); background: rgba(239,68,68,.08); color: rgba(153, 27, 27, 1); }
  .bb-btn{
    display:inline-flex; align-items:center; justify-content:center;
    padding: 8px 12px; border-radius: 12px; font-weight: 900; font-size: 12px;
    border: 1px solid rgba(17,24,39,.10);
    background: white; color: var(--bb-ink); text-decoration:none;
    cursor: pointer;
  }
  .bb-btn.primary{
    border-color: rgba(201,162,74,.45);
    background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(201,162,74,.10));
    box-shadow: 0 10px 22px rgba(201,162,74,.14);
  }
  .bb-filters{ display:flex; gap:10px; flex-wrap:wrap; }
  .bb-input{
    padding: 10px 12px; border-radius: 12px;
    border: 1px solid rgba(17,24,39,.10);
    background: rgba(255,255,255,.85);
    font-weight: 800; color: var(--bb-ink);
  }
</style>
@endonce

<div class="bb-wrap">
  <div class="bb-head">
    <div>
      <h1 class="bb-h1">Pagos</h1>
      <p class="bb-sub">Controla pagos y confirma citas desde el CRM.</p>
    </div>

    <form method="GET" class="bb-filters">
      <input class="bb-input" type="text" name="q" value="{{ request('q') }}" placeholder="Buscar cliente / ID cita">
      <select class="bb-input" name="estado_pago">
        <option value="">Estado pago (todos)</option>
        <option value="pendiente" @selected(request('estado_pago')==='pendiente')>Pendiente</option>
        <option value="pagado" @selected(request('estado_pago')==='pagado')>Pagado</option>
        <option value="cancelado" @selected(request('estado_pago')==='cancelado')>Cancelado</option>
      </select>
      <button class="bb-btn primary" type="submit">Filtrar</button>
      <a class="bb-btn" href="{{ url()->current() }}">Limpiar</a>
    </form>
  </div>

  <div class="bb-card">
    @if($citas->count() === 0)
      <div style="color: rgba(107,114,128,.92); font-weight: 800;">
        No hay registros para mostrar.
      </div>
    @else
      <table class="bb-table">
        <thead>
          <tr>
            <th style="width:90px;">Cita</th>
            <th>Cliente</th>
            <th style="width:170px;">Fecha / Hora</th>
            <th style="width:120px;">Total</th>
            <th style="width:150px;">Estado pago</th>
            <th style="width:150px;">Estado cita</th>
            <th style="width:290px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($citas as $cita)
            @php
              $id = $cita->id ?? $cita->id_cita ?? null;

              $cliente = $cita->cliente->nombre ?? $cita->cliente->nombre_completo ?? $cita->nombre_cliente ?? '—';

              $fecha = $cita->fecha ?? $cita->fecha_cita ?? null;
              $hora  = $cita->hora  ?? $cita->hora_cita  ?? null;

              $total = $cita->total ?? $cita->total_pagar ?? $cita->monto ?? null;

              $estadoPago = $cita->estado_pago ?? $cita->pago_estado ?? 'pendiente';
              $estadoCita = $cita->estado ?? $cita->estado_cita ?? '—';

              [$bpCls, $bpTxt] = $badge($estadoPago);
              [$bcCls, $bcTxt] = $badge($estadoCita);
            @endphp

            <tr>
              <td>#{{ $id }}</td>
              <td>{{ $cliente }}</td>
              <td>
                {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('Y-m-d') : '—' }}
                <div style="font-size:12px; color: rgba(107,114,128,.92); font-weight:800;">
                  {{ $hora ? \Carbon\Carbon::parse($hora)->format('H:i') : '—' }}
                </div>
              </td>
              <td>{{ $money($total) }}</td>
              <td><span class="{{ $bpCls }}">{{ $bpTxt }}</span></td>
              <td><span class="{{ $bcCls }}">{{ $bcTxt }}</span></td>
              <td style="display:flex; gap:8px; flex-wrap:wrap;">
                <a class="bb-btn" href="{{ $urlShowPago($id) }}">Ver</a>

                <form method="POST" action="{{ $urlMarcarPagado($id) }}">
                  @csrf
                  @method('PATCH')
                  <button class="bb-btn primary" type="submit"
                    onclick="return confirm('¿Marcar este pago como recibido?')">
                    Marcar pagado
                  </button>
                </form>

                <form method="POST" action="{{ $urlConfirmarCita($id) }}">
                  @csrf
                  @method('PATCH')
                  <button class="bb-btn" type="submit"
                    onclick="return confirm('¿Confirmar esta cita?')">
                    Confirmar cita
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection
