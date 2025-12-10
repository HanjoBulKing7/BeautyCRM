@component('mail::message')
# Cita confirmada

Hola {{ optional($cita->cliente)->name ?? 'cliente' }},

Tu cita en **{{ config('app.name') }}** ha sido confirmada.

@component('mail::panel')
**Servicio:** {{ optional($cita->servicio)->nombre_servicio ?? 'Servicio' }}  
**Fecha:** {{ $fecha_legible }}  
**Hora:** {{ $hora }}  
**Profesional:** {{ optional($cita->empleado)->name ?? 'Por asignar' }}  
@endcomponent

@if(!empty($googleEventLink))
Puedes ver o gestionar tu cita desde Google Calendar en el siguiente enlace:

@component('mail::button', ['url' => $googleEventLink])
Ver en Google Calendar
@endcomponent
@endif

@if(!empty($cita->observaciones))
> **Notas de la cita:**  
> {{ $cita->observaciones }}
@endif

Gracias por confiar en nosotros 💅  
**{{ config('app.name') }}**
@endcomponent
