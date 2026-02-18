@component('mail::message')

# ✨ ¡Tu cita ha sido confirmada!

**Servicio:** {{ $cita->servicio->nombre_servicio }}

**Fecha:** {{ $fecha_legible }}

**Hora:** {{ $hora }}

**Duración:** {{ $cita->servicio->duracion ?? 60 }} minutos

@if($googleEventLink)
@component('mail::button', ['url' => $googleEventLink])
📅 Ver en Google Calendar
@endcomponent
@endif

Te esperamos en el salón 💇‍♀️✨

Gracias,<br>
{{ config('app.name') }}

@endcomponent
