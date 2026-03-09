@php
  $layout = view()->exists('layouts.admin') ? 'layouts.admin' : 'layouts.app';
@endphp

@extends('layouts.website')

@section('content')
@once
  <style>
    :root{
      --bb-gold: #c9a24a;
      --bb-gold-light: rgba(201,162,74,0.15);
      --bb-ink: #1a1e2b;
      --bb-muted: #6b7280;
      --bb-glass: rgba(255,255,255,0.92);
      --bb-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    }
    
    body {
      background: linear-gradient(145deg, #f9fafc 0%, #f3f4f6 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
    }
    
    .bb-wrap { 
      max-width: 560px; 
      margin: 0 auto; 
      padding: 20px; 
      width: 100%;
    }
    
    .bb-card {
      background: var(--bb-glass);
      border: 1px solid rgba(255,255,255,0.8);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      box-shadow: var(--bb-shadow);
      border-radius: 32px;
      padding: 40px 32px;
      text-align: center;
      transition: all 0.3s ease;
    }
    
    .bb-icon {
      width: 80px;
      height: 80px;
      background: var(--bb-gold-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      border: 1px solid rgba(201,162,74,0.3);
    }
    
    .bb-icon svg {
      width: 40px;
      height: 40px;
      fill: none;
      stroke: var(--bb-gold);
      stroke-width: 2;
    }
    
    .bb-h1 { 
      font-size: 28px; 
      font-weight: 700; 
      color: var(--bb-ink); 
      margin: 0 0 12px;
      letter-spacing: -0.02em;
    }
    
    .bb-sub { 
      color: var(--bb-muted); 
      margin: 0 0 24px;
      font-size: 16px;
      line-height: 1.6;
      max-width: 380px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .bb-timer {
      display: inline-block;
      background: var(--bb-gold-light);
      color: var(--bb-gold);
      font-weight: 600;
      font-size: 14px;
      padding: 6px 16px;
      border-radius: 40px;
      margin-bottom: 28px;
      border: 1px solid rgba(201,162,74,0.2);
    }
    
    .bb-timer span {
      font-weight: 800;
      font-size: 16px;
      margin-left: 4px;
    }
    
    .bb-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 20px;
    }
    
    .bb-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 14px 24px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 15px;
      border: 1px solid rgba(17,24,39,0.08);
      background: white;
      color: var(--bb-ink);
      text-decoration: none;
      transition: all 0.2s ease;
      min-width: 160px;
      cursor: pointer;
    }
    
    .bb-btn i {
      margin-right: 8px;
      font-size: 18px;
    }
    
    .bb-btn.primary {
      border-color: var(--bb-gold);
      background: linear-gradient(135deg, #ffffff 0%, rgba(201,162,74,0.1) 100%);
      color: #856e3a;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(201,162,74,0.2);
    }
    
    .bb-btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(201,162,74,0.3);
      background: linear-gradient(135deg, #ffffff 0%, rgba(201,162,74,0.15) 100%);
    }
    
    .bb-btn.outline {
      background: transparent;
      border: 1px solid rgba(107,114,128,0.3);
      backdrop-filter: blur(4px);
    }
    
    .bb-btn.outline:hover {
      background: rgba(255,255,255,0.5);
      border-color: var(--bb-gold);
    }
    
    .bb-note {
      font-size: 14px;
      color: #9ca3af;
      margin-top: 16px;
    }
  </style>
@endonce

@include('beauty.partials.whatsapp-icon')
@include('beauty.partials.header')

<div class="bb-wrap">
  <div class="bb-card">
    
    {{-- Ícono de cancelación --}}
    <div class="bb-icon">
      <svg viewBox="0 0 24 24" stroke="currentColor">
        <circle cx="12" cy="12" r="10" />
        <line x1="15" y1="9" x2="9" y2="15" />
        <line x1="9" y1="9" x2="15" y2="15" />
      </svg>
    </div>
    
    {{-- Título --}}
    <h1 class="bb-h1">Pago cancelado</h1>
    
    {{-- Descripción --}}
    <p class="bb-sub">
      No se realizó ningún cargo a tu tarjeta. 
      Si esto fue un error o cambiaste de opinión, podés intentarlo nuevamente.
    </p>
    
    {{-- Timer --}}
    <div class="bb-timer">
      ⏱️ Redirigiendo en <span id="countdown">15</span> segundos
    </div>
    
    {{-- Botones de acción --}}
    <div class="bb-actions">
      <a class="bb-btn primary" href="{{ url()->previous() }}">
        <i class='bx bx-credit-card'></i>
        Intentar otra vez
      </a>
      <a class="bb-btn outline" href="{{ url('/mis-reservas') }}" id="goToReservas">
        <i class='bx bx-calendar'></i>
        Ir a mis reservas
      </a>
    </div>
    
    {{-- Nota --}}
    <p class="bb-note">
      Si no eres redirigido automáticamente, haz clic en "Ir a mis reservas"
    </p>
    
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const countdownEl = document.getElementById('countdown');
    const goToReservasBtn = document.getElementById('goToReservas');
    
    // Tiempo en segundos
    let timeLeft = 15;
    
    // Actualizar el contador cada segundo
    const timer = setInterval(function() {
      timeLeft--;
      countdownEl.textContent = timeLeft;
      
      // Cuando llegue a 0, redirigir
      if (timeLeft <= 0) {
        clearInterval(timer);
        window.location.href = "{{ url('/mis-reservas') }}";
      }
    }, 1000);
    
    // Si el usuario hace clic en "Ir a mis reservas" antes del timer, cancelamos el timer
    goToReservasBtn.addEventListener('click', function(e) {
      clearInterval(timer);
    });
  });
</script>

{{-- Boxicons para íconos --}}
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

@endsection