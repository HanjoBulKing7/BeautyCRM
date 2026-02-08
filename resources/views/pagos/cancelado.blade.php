@php
  $layout = view()->exists('layouts.admin') ? 'layouts.admin' : 'layouts.app';
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
    .bb-wrap{ max-width: 860px; margin: 0 auto; padding: 24px 16px; }
    .bb-card{
      background: var(--bb-glass);
      border: 1px solid rgba(255,255,255,.65);
      backdrop-filter: blur(14px) saturate(140%);
      -webkit-backdrop-filter: blur(14px) saturate(140%);
      box-shadow: 0 10px 26px rgba(17,24,39,.06);
      border-radius: 18px;
      padding: 18px;
    }
    .bb-h1{ font-size: 22px; font-weight: 900; color: var(--bb-ink); margin:0 0 6px; }
    .bb-sub{ color: var(--bb-muted); margin:0 0 14px; }
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
  </style>
@endonce

<div class="bb-wrap">
  <div class="bb-card">
    <h1 class="bb-h1">Pago cancelado</h1>
    <p class="bb-sub">No se realizó ningún cobro. Puedes intentarlo de nuevo cuando quieras.</p>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a class="bb-btn primary" href="{{ url()->previous() }}">Intentar otra vez</a>
      <a class="bb-btn" href="{{ url('/') }}">Volver al inicio</a>
    </div>
  </div>
</div>
@endsection
