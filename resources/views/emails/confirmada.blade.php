@component('mail::message')

<div style="text-align:center; margin-bottom: 24px;">
    <h1 style="color:#8e6708; font-size:22px; margin:0;">✨ ¡Tu cita está confirmada!</h1>
    <p style="color:#64748b; margin-top:6px;">Gracias por elegirnos. Aquí tienes los detalles de tu reserva.</p>
</div>

---

@php
    $serviciosNombres = $cita->servicios->pluck('nombre_servicio')->implode(', ');
    $empleadoNombre   = optional($cita->empleado)->nombre . ' ' . optional($cita->empleado)->apellido;
@endphp

**📋 Servicio(s):** {{ $serviciosNombres ?: 'Ver detalles en el salón' }}

**📅 Fecha:** {{ $fecha_legible }}

**🕐 Hora:** {{ $hora }}

**💇 Estilista:** {{ trim($empleadoNombre) ?: 'Por asignar' }}

---

@if($googleEventLink)
@component('mail::button', ['url' => $googleEventLink, 'color' => 'primary'])
📅 Agregar a Google Calendar
@endcomponent
@endif

@component('mail::button', ['url' => config('app.url') . '/agendar-cita', 'color' => 'success'])
📲 Agendar otra cita
@endcomponent

---

<p style="font-size:13px; color:#64748b; text-align:center;">
    ¿Necesitas cambiar o cancelar tu cita? Escríbenos a
    <a href="mailto:contacto@beautybonita.com.mx">contacto@beautybonita.com.mx</a>
    o por WhatsApp.
</p>

Gracias,<br>
**{{ config('app.name') }}**

@endcomponent
