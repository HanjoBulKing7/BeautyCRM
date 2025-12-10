@component('mail::message')
# Hola {{ $cita->cliente->name }},

Gracias por agendar tu cita en **{{ config('app.name') }}**.

@component('mail::panel')
**Fecha:** {{ $fecha_legible }}  
**Horario:** {{ $hora_inicio }} – {{ $hora_fin }} (Hora local)  
**Servicio:** {{ $cita->servicio->nombre_servicio }}  
**Profesional:** {{ optional($cita->empleado)->name ?? 'Por asignar' }}  
**Lugar:** {{ config('app.salon_address', 'Salón de Belleza') }}
@endcomponent

@isset($googleEventLink)
@component('mail::button', ['url' => $googleEventLink])
Ver cita en Google Calendar
@endcomponent
@endisset

Si necesitas reprogramar o cancelar tu cita, por favor contáctanos:

- Teléfono: {{ config('app.salon_phone', 'N/A') }}  
- Correo: {{ config('mail.from.address') }}

Gracias por confiar en nosotros.  
Te esperamos.

Saludos cordiales,  
**{{ config('app.name') }}**
@endcomponent
