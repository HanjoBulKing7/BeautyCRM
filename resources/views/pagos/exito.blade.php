@php
  $layout = view()->exists('layouts.admin') ? 'layouts.admin' : 'layouts.app';

  $money = function ($cents, $currency = 'usd') {
      if ($cents === null || $cents === '') return '—';
      $amount = ((float)$cents) / 100;
      $cur = strtoupper($currency ?: 'USD');
      return $cur.' $'.number_format($amount, 2, '.', ',');
  };

  $safe = fn($v) => ($v === null || $v === '') ? '—' : $v;
@endphp

@extends($layout)

@section('content')
@once
  <style>
    :root{
      --bb-gold: rgba(201,162,74,.95);
      --bb-gold-soft: rgba(201,162,74,.14);
      --bb-ink: rgba(17,24,39,.92);
      --bb-muted: rgba(107,114,128,.92);
      --bb-glass: rgba(255,255,255,.72);
      --bb-border: rgba(17,24,39,.08);
    }
    .bb-wrap{ max-width: 980px; margin: 0 auto; padding: 24px 16px; }
    .bb-card{
      background: var(--bb-glass);
      border: 1px solid rgba(255,255,255,.65);
      backdrop-filter: blur(14px) saturate(140%);
      -webkit-backdrop-filter: blur(14px) saturate(140%);
      box-shadow: 0 10px 26px rgba(17,24,39,.06);
      border-radius: 18px;
      padding: 18px;
    }
    .bb-h1{ font-size: 22px; font-weight: 800; color: var(--bb-ink); margin: 0 0 6px; }
    .bb-sub{ color: var(--bb-muted); margin: 0 0 16px; }
    .bb-badge{
      display:inline-flex; align-items:center; gap:8px;
      padding: 6px 10px; border-radius: 999px;
      border: 1px solid rgba(17,24,39,.10);
      font-weight: 700; font-size: 12px;
      background: white;
    }
    .bb-badge.ok{ border-color: rgba(16,185,129,.25); background: rgba(16,185,129,.08); color: rgba(5, 120, 87, 1); }
    .bb-badge.warn{ border-color: rgba(245,158,11,.28); background: rgba(245,158,11,.10); color: rgba(146, 64, 14, 1); }
    .bb-badge.bad{ border-color: rgba(239,68,68,.25); background: rgba(239,68,68,.08); color: rgba(153, 27, 27, 1); }

    .bb-table{ width:100%; border-collapse: collapse; overflow:hidden; border-radius: 14px; }
    .bb-table th, .bb-table td{ padding: 12px 12px; border-bottom: 1px solid rgba(17,24,39,.08); text-align:left; }
    .bb-table th{ font-size: 12px; color: var(--bb-muted); font-weight: 800; background: rgba(255,255,255,.65); }
    .bb-table td{ color: var(--bb-ink); font-weight: 600; background: rgba(255,255,255,.85); }

    .bb-actions{ display:flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
    .bb-btn{
      display:inline-flex; align-items:center; justify-content:center;
      padding: 10px 14px; border-radius: 12px; font-weight: 800;
      border: 1px solid rgba(17,24,39,.10);
      background: white; color: var(--bb-ink); text-decoration:none;
    }
    .bb-btn.primary{
      border-color: rgba(201,162,74,.45);
      background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(201,162,74,.10));
      box-shadow: 0 10px 22px rgba(201,162,74,.14);
    }
    .bb-btn.ghost{ background: rgba(255,255,255,.7); }
    .bb-btn:hover{ transform: translateY(-1px); transition: .15s ease; }
    .bb-code{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 12px; padding: 6px 8px; border-radius: 10px;
      border: 1px solid rgba(17,24,39,.10); background: rgba(255,255,255,.9);
      word-break: break-all;
    }
    .bb-alert{
      border-radius: 16px; padding: 14px;
      border: 1px solid rgba(239,68,68,.25);
      background: rgba(239,68,68,.08);
      color: rgba(153, 27, 27, 1);
      font-weight: 700;
      margin: 12px 0 0;
    }
  </style>
@endonce

<div class="bb-wrap">
  <div class="bb-card">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
      <div>
        <h1 class="bb-h1">Pago realizado</h1>
        <p class="bb-sub">Stripe regresó una sesión válida. Aquí están los detalles del cobro.</p>
      </div>

      @php
        $status = $payment_intent->status ?? ($session->payment_status ?? null);
        $badgeClass = 'warn';
        if (in_array($status, ['succeeded', 'paid'])) $badgeClass = 'ok';
        if (in_array($status, ['canceled', 'requires_payment_method', 'failed'])) $badgeClass = 'bad';
      @endphp
      <span class="bb-badge {{ $badgeClass }}">
        Estado: {{ $safe($status) }}
      </span>
    </div>

    @if(isset($error))
      <div class="bb-alert">
        Error al recuperar la sesión de Stripe: {{ $error }}
      </div>
    @else
      @php
        $sessionId = $session->id ?? null;
        $currency  = $session->currency ?? ($payment_intent->currency ?? 'usd');
        $amount    = $payment_intent->amount_received ?? $payment_intent->amount ?? null;

        $email = $session->customer_details->email
                 ?? $session->customer_email
                 ?? null;

        $piId = $payment_intent->id ?? ($session->payment_intent ?? null);
      @endphp

      <table class="bb-table" style="margin-top: 14px;">
        <thead>
          <tr>
            <th style="width: 220px;">Campo</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Session ID</td>
            <td>
              <span class="bb-code" id="bbSessionId">{{ $safe($sessionId) }}</span>
              <button type="button" class="bb-btn ghost" style="margin-left:8px; padding:6px 10px;"
                onclick="navigator.clipboard.writeText(document.getElementById('bbSessionId').innerText)">
                Copiar
              </button>
            </td>
          </tr>
          <tr>
            <td>Payment Intent</td>
            <td><span class="bb-code">{{ $safe($piId) }}</span></td>
          </tr>
          <tr>
            <td>Monto</td>
            <td>{{ $money($amount, $currency) }}</td>
          </tr>
          <tr>
            <td>Moneda</td>
            <td>{{ strtoupper($currency ?: 'USD') }}</td>
          </tr>
          <tr>
            <td>Email</td>
            <td>{{ $safe($email) }}</td>
          </tr>
          <tr>
            <td>Creado</td>
            <td>
              @php
                $created = $session->created ?? null;
              @endphp
              {{ $created ? \Carbon\Carbon::createFromTimestamp($created)->format('Y-m-d H:i') : '—' }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="bb-actions">
        <a class="bb-btn primary" href="{{ url('/') }}">Volver al inicio</a>
        <a class="bb-btn" href="{{ url('/admin/pagos') }}">Ir a panel de pagos</a>
      </div>

      <p class="bb-sub" style="margin-top: 12px;">
        Nota: con tu controller actual, Stripe no está ligado a una cita específica (no manda <span class="bb-code">cita_id</span> ni metadata).
        Por eso la confirmación de cita se hace desde el panel admin de pagos.
      </p>
    @endif
  </div>
</div>
@endsection
