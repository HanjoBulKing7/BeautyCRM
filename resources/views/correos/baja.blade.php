<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Preferencias de correo — Beauty Bonita</title>
<style>
  body { margin:0; background:#f5f0eb; font-family: Arial, sans-serif; color:#0f172a; }
  .wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:32px 16px; box-sizing:border-box; }
  .card { background:#fff; max-width:460px; width:100%; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,.08); text-align:center; }
  .top { padding:32px 32px 20px; border-bottom:3px solid #C8A64A; }
  .top img { width:64px; height:64px; border-radius:50%; border:2px solid #C8A64A; }
  .brand { margin:10px 0 0; font-size:11px; letter-spacing:4px; color:#8e6708; text-transform:uppercase; }
  .body { padding:28px 32px 34px; }
  h1 { font-family: Georgia, serif; font-weight:normal; font-size:24px; margin:0 0 12px; }
  p { margin:0 0 14px; color:#475569; line-height:1.6; font-size:15px; overflow-wrap:anywhere; }
  .btn { display:inline-block; border:0; cursor:pointer; border-radius:999px; padding:12px 28px; font-size:15px; font-weight:bold; }
  .btn-gold { background:linear-gradient(135deg,#C8A64A,#8e6708); color:#fff; }
  .btn-line { background:#fff; color:#0f172a; border:1.5px solid #0f172a; }
  .small { font-size:12px; color:#94a3b8; margin-top:18px; }
  .ok { color:#15803d; font-weight:bold; }
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="top">
      <img src="{{ asset('images/logo-email.png') }}" alt="Beauty Bonita">
      <p class="brand">Beauty Bonita</p>
    </div>
    <div class="body">
      @php $nombre = trim(explode(' ', (string) $cliente->nombre)[0] ?? ''); @endphp

      @if(!empty($recienDadoDeAlta))
        <h1>¡Qué gusto tenerte de vuelta{{ $nombre ? ', ' . $nombre : '' }}!</h1>
        <p class="ok">Volverás a recibir nuestras promociones y tu regalo de cumpleaños.</p>
      @elseif($cliente->baja_correos_at)
        <h1>Listo{{ $nombre ? ', ' . $nombre : '' }}</h1>
        <p>Ya no recibirás correos de promociones de Beauty Bonita.</p>
        <p>Seguirás recibiendo los correos de tus citas, como la confirmación de una reserva.</p>
        <form method="POST" action="{{ $urlAlta }}">
          <button type="submit" class="btn btn-line">Volver a recibir promociones</button>
        </form>
        <p class="small">Si fue un error, puedes volver a suscribirte cuando quieras.</p>
      @else
        <h1>¿Ya no quieres recibir nuestros correos?</h1>
        <p>Dejarás de recibir promociones, descuentos y tu regalo de cumpleaños en <strong>{{ $cliente->email }}</strong>.</p>
        <form method="POST" action="{{ $urlBaja }}">
          <input type="hidden" name="desde_pagina" value="1">
          <button type="submit" class="btn btn-gold">Sí, no quiero recibir más correos</button>
        </form>
        <p class="small">Los correos de tus citas (confirmaciones de reserva) se seguirán enviando.</p>
      @endif

      <p class="small"><a href="{{ url('/home') }}" style="color:#8e6708;">Ir a Beauty Bonita</a></p>
    </div>
  </div>
</div>
</body>
</html>
