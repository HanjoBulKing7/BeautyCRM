<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Gracias por tu visita — Beauty Bonita</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f0eb;font-family:Georgia,serif;">
@php
  $logoSrc = isset($message) ? $message->embed(public_path('images/logo-email.png')) : asset('images/logo-email.png');
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f0eb;padding:40px 16px;">
<tr><td align="center">
<table width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;">

  <tr>
    <td align="center" style="background:#ffffff;border-radius:20px 20px 0 0;padding:36px 40px 28px;border-bottom:1px solid #f0e8d8;">
      <img src="{{ $logoSrc }}" alt="Beauty Bonita" width="72" height="72"
           style="display:block;margin:0 auto 14px;border-radius:50%;border:2px solid #C8A64A;background:#ffffff;">
      <p style="margin:0;font-size:11px;letter-spacing:4px;color:#8e6708;text-transform:uppercase;font-family:Arial,sans-serif;">Beauty Bonita</p>
    </td>
  </tr>

  <tr><td style="background:linear-gradient(90deg,#C8A64A,#e4c76b,#C8A64A);height:3px;"></td></tr>

  <tr>
    <td align="center" style="background:#ffffff;padding:44px 48px 24px;">
      <p style="margin:0 0 6px;font-size:44px;line-height:1;">💛</p>
      <h1 style="margin:12px 0 6px;font-size:28px;color:#0f172a;font-weight:normal;line-height:1.3;">
        ¡Gracias por tu visita,<br>
        <strong style="color:#8e6708;">{{ $nombreCliente }}</strong>!
      </h1>
      <p style="margin:0;font-size:13px;letter-spacing:3px;color:#C8A64A;text-transform:uppercase;font-family:Arial,sans-serif;">Fue un gusto atenderte</p>
    </td>
  </tr>

  <tr>
    <td style="background:#ffffff;padding:0 48px 30px;">
      <p style="margin:0;font-size:16px;color:#334155;line-height:1.8;text-align:center;">
        Gracias por elegirnos y confiar en nuestro equipo{!! !empty($serviciosNombres) ? ' para tu <strong>' . e($serviciosNombres) . '</strong>' : '' !!}. Esperamos que te hayas sentido consentida y que ames tu resultado. ✨
      </p>
    </td>
  </tr>

  <tr>
    <td style="background:#ffffff;padding:0 48px;">
      <div style="height:1px;background:linear-gradient(90deg,transparent,#C8A64A,transparent);"></div>
    </td>
  </tr>

  <tr>
    <td style="background:#ffffff;padding:34px 48px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1.5px solid #C8A64A;border-radius:16px;">
        <tr>
          <td align="center" style="background:#fffbf2;border-radius:16px;padding:30px 24px;">
            <p style="margin:0 0 6px;font-size:12px;letter-spacing:3px;color:#8e6708;text-transform:uppercase;font-family:Arial,sans-serif;">Tu opinión nos ayuda mucho</p>
            <p style="margin:6px 0 12px;font-size:30px;letter-spacing:6px;color:#C8A64A;line-height:1;">★★★★★</p>
            <p style="margin:0 0 22px;font-size:15px;color:#334155;font-family:Arial,sans-serif;line-height:1.6;">
              ¿Nos regalas un minuto para dejar tu reseña en Google Maps?<br>
              Así más personas pueden conocer Beauty Bonita.
            </p>
            <a href="{{ $urlResena }}"
               style="display:inline-block;background:linear-gradient(135deg,#C8A64A 0%,#8e6708 100%);color:#ffffff;text-decoration:none;font-size:15px;font-family:Arial,sans-serif;font-weight:bold;padding:14px 38px;border-radius:50px;">
              ⭐ Dejar mi reseña
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td align="center" style="background:#ffffff;padding:0 48px 40px;">
      <p style="margin:0 0 14px;font-size:14px;color:#64748b;font-family:Arial,sans-serif;">¿Lista para tu próxima cita?</p>
      <a href="{{ $urlReserva }}"
         style="display:inline-block;border:1.5px solid #0f172a;color:#0f172a;text-decoration:none;font-size:14px;font-family:Arial,sans-serif;font-weight:bold;padding:11px 30px;border-radius:50px;">
        Agendar nueva cita
      </a>
    </td>
  </tr>

  <tr>
    <td align="center" style="background:#0f172a;border-radius:0 0 20px 20px;padding:28px 40px;">
      <img src="{{ $logoSrc }}" alt="" width="32" height="32"
           style="display:block;margin:0 auto 10px;border-radius:50%;background:#ffffff;">
      <p style="margin:0 0 6px;font-size:12px;color:#C8A64A;font-family:Arial,sans-serif;letter-spacing:2px;text-transform:uppercase;">Beauty Bonita</p>
      <p style="margin:0 0 10px;font-size:11px;color:#94a3b8;font-family:Arial,sans-serif;line-height:1.6;">
        ¿Algo que podamos mejorar? Respóndenos a este correo o escríbenos a
        <a href="mailto:contacto@beautybonita.com.mx" style="color:#C8A64A;text-decoration:none;">contacto@beautybonita.com.mx</a>
      </p>
      <p style="margin:0;font-size:10px;color:#64748b;font-family:Arial,sans-serif;">
        Recibiste este correo porque visitaste Beauty Bonita.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
