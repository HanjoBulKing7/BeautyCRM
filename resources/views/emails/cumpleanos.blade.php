<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>¡Feliz Cumpleaños de Beauty Bonita!</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f0eb;font-family:Georgia,serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f0eb;padding:40px 16px;">
<tr><td align="center">
<table width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;">

  {{-- Logo header --}}
  <tr>
    <td align="center" style="background:#ffffff;border-radius:20px 20px 0 0;padding:36px 40px 28px;border-bottom:1px solid #f0e8d8;">
      <img src="https://beautybonita.com.mx/images/favicon.png"
           alt="Beauty Bonita" width="72" height="72"
           style="display:block;margin:0 auto 14px;border-radius:50%;border:2px solid #C8A64A;">
      <p style="margin:0;font-size:11px;letter-spacing:4px;color:#8e6708;text-transform:uppercase;font-family:Arial,sans-serif;">Beauty Bonita</p>
    </td>
  </tr>

  {{-- Gold bar --}}
  <tr><td style="background:linear-gradient(90deg,#C8A64A,#e4c76b,#C8A64A);height:3px;"></td></tr>

  {{-- Hero --}}
  <tr>
    <td align="center" style="background:#ffffff;padding:48px 48px 36px;">
      <p style="margin:0 0 6px;font-size:48px;line-height:1;">🎂</p>
      <h1 style="margin:12px 0 6px;font-size:30px;color:#0f172a;font-weight:normal;line-height:1.3;">
        ¡Feliz Cumpleaños,<br>
        <strong style="color:#8e6708;">{{ $nombreCliente }}</strong>!
      </h1>
      <p style="margin:0;font-size:13px;letter-spacing:3px;color:#C8A64A;text-transform:uppercase;font-family:Arial,sans-serif;">Hoy es tu día ✨</p>
    </td>
  </tr>

  {{-- Message --}}
  <tr>
    <td style="background:#ffffff;padding:0 48px 32px;">
      <p style="margin:0 0 18px;font-size:16px;color:#334155;line-height:1.8;text-align:center;">
        En <strong>Beauty Bonita</strong> queremos celebrarte como mereces.<br>
        Gracias por confiar en nosotras para lucir siempre increíble. 💇‍♀️
      </p>
    </td>
  </tr>

  {{-- Divider --}}
  <tr>
    <td style="background:#ffffff;padding:0 48px;">
      <div style="height:1px;background:linear-gradient(90deg,transparent,#C8A64A,transparent);"></div>
    </td>
  </tr>

  {{-- Gift coupon --}}
  <tr>
    <td style="background:#ffffff;padding:36px 48px;">
      <table width="100%" cellpadding="0" cellspacing="0"
             style="border:1.5px solid #C8A64A;border-radius:16px;overflow:hidden;">
        <tr>
          <td align="center" style="background:#fffbf2;padding:32px 24px;">
            <p style="margin:0 0 4px;font-size:12px;letter-spacing:3px;color:#8e6708;text-transform:uppercase;font-family:Arial,sans-serif;">🎁 Tu regalo de cumpleaños</p>
            <p style="margin:10px 0 4px;font-size:52px;font-weight:bold;color:#0f172a;line-height:1;font-family:Arial,sans-serif;">15%</p>
            <p style="margin:0 0 20px;font-size:16px;color:#334155;font-family:Arial,sans-serif;">de descuento en cualquier servicio</p>

            {{-- Coupon code --}}
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background:#0f172a;border-radius:8px;padding:10px 28px;">
                  <p style="margin:0;font-size:18px;font-family:'Courier New',monospace;color:#C8A64A;letter-spacing:4px;font-weight:bold;">{{ $codigoCupon }}</p>
                </td>
              </tr>
            </table>

            <p style="margin:14px 0 0;font-size:12px;color:#94a3b8;font-family:Arial,sans-serif;">
              Válido durante tu mes de cumpleaños • Un uso por cliente
            </p>
          </td>
        </tr>

        {{-- Dashed separator --}}
        <tr>
          <td style="position:relative;height:0;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="40" style="background:#f5f0eb;border-radius:0 20px 20px 0;height:24px;"></td>
                <td style="border-top:2px dashed #e2c87a;height:24px;vertical-align:middle;"></td>
                <td width="40" style="background:#f5f0eb;border-radius:20px 0 0 20px;height:24px;"></td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td align="center" style="background:#fffbf2;padding:20px 24px 28px;">
            <p style="margin:0 0 16px;font-size:13px;color:#64748b;font-family:Arial,sans-serif;">
              Aplica en todos los servicios del salón
            </p>
            <a href="{{ $urlReserva }}"
               style="display:inline-block;background:linear-gradient(135deg,#C8A64A 0%,#8e6708 100%);color:#ffffff;text-decoration:none;font-size:15px;font-family:Arial,sans-serif;font-weight:bold;padding:14px 40px;border-radius:50px;letter-spacing:0.5px;">
              ✨ Quiero mi regalo
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Services --}}
  <tr>
    <td style="background:#ffffff;padding:0 48px 40px;">
      <table width="100%" cellpadding="0" cellspacing="0"
             style="background:#f8fafc;border-radius:12px;padding:20px;">
        <tr>
          <td align="center" style="padding:12px 8px;">
            <p style="margin:0 0 14px;font-size:11px;letter-spacing:3px;color:#8e6708;text-transform:uppercase;font-family:Arial,sans-serif;">Nuestros servicios</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="font-size:22px;">💅</td>
                <td align="center" style="font-size:22px;">💇</td>
                <td align="center" style="font-size:22px;">💄</td>
                <td align="center" style="font-size:22px;">💆</td>
              </tr>
              <tr>
                <td align="center" style="font-size:11px;color:#64748b;font-family:Arial,sans-serif;padding-top:4px;">Uñas</td>
                <td align="center" style="font-size:11px;color:#64748b;font-family:Arial,sans-serif;padding-top:4px;">Peinado</td>
                <td align="center" style="font-size:11px;color:#64748b;font-family:Arial,sans-serif;padding-top:4px;">Maquillaje</td>
                <td align="center" style="font-size:11px;color:#64748b;font-family:Arial,sans-serif;padding-top:4px;">Masaje</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Footer --}}
  <tr>
    <td align="center" style="background:#0f172a;border-radius:0 0 20px 20px;padding:28px 40px;">
      <img src="https://beautybonita.com.mx/images/favicon.png"
           alt="" width="32" height="32"
           style="display:block;margin:0 auto 10px;border-radius:50%;opacity:.7;">
      <p style="margin:0 0 6px;font-size:12px;color:#C8A64A;font-family:Arial,sans-serif;letter-spacing:2px;text-transform:uppercase;">Beauty Bonita</p>
      <p style="margin:0 0 10px;font-size:11px;color:#475569;font-family:Arial,sans-serif;line-height:1.6;">
        ¿Necesitas ayuda? Escríbenos a
        <a href="mailto:contacto@beautybonita.com.mx" style="color:#C8A64A;text-decoration:none;">contacto@beautybonita.com.mx</a>
      </p>
      <p style="margin:0;font-size:10px;color:#334155;font-family:Arial,sans-serif;">
        Recibiste este correo porque eres cliente de Beauty Bonita.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
