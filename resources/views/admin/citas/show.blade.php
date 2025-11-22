@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-eye"></i> Detalle de Cita #{{ $cita->id_cita }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-user text-primary"></i> Información del Cliente</h5>
                            <p><strong>Nombre:</strong> {{ $cita->cliente->name }}</p>
                            <p><strong>Email:</strong> {{ $cita->cliente->email }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-cut text-primary"></i> Información del Servicio</h5>
                            <p><strong>Servicio:</strong> {{ $cita->servicio->nombre_servicio }}</p>
                            <p><strong>Duración:</strong> {{ $cita->servicio->duracion_minutos }} minutos</p>
                            <p><strong>Precio:</strong> ${{ number_format($cita->servicio->precio, 2) }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-calendar-alt text-primary"></i> Fecha y Hora</h5>
                            <p><strong>Fecha:</strong> {{ $cita->fecha_cita->format('d/m/Y') }}</p>
                            <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($cita->hora_cita)->format('h:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-info-circle text-primary"></i> Estado y Empleado</h5>
                            <p>
                                <strong>Estado:</strong> 
                                <span class="badge bg-{{ 
                                    $cita->estado_cita === 'completada' ? 'success' : 
                                    ($cita->estado_cita === 'confirmada' ? 'primary' : 
                                    ($cita->estado_cita === 'cancelada' ? 'danger' : 'warning')) 
                                }}">
                                    {{ ucfirst($cita->estado_cita) }}
                                </span>
                            </p>
                            <p><strong>Empleado:</strong> {{ $cita->empleado ? $cita->empleado->nombre : 'Sin asignar' }}</p>
                        </div>
                    </div>

                    @if($cita->observaciones)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2"><i class="fas fa-comment text-primary"></i> Observaciones</h5>
                            <p>{{ $cita->observaciones }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2"><i class="fab fa-google text-primary"></i> Sincronización Google Calendar</h5>
                        @if($cita->synced_with_google)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Esta cita está sincronizada con Google Calendar
                                @if($cita->google_event_id)
                                    <br><small class="text-muted">ID del evento: {{ $cita->google_event_id }}</small>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Esta cita no está sincronizada con Google Calendar
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.citas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <div>
                            <a href="{{ route('admin.citas.edit', $cita) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('admin.citas.destroy', $cita) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('¿Eliminar esta cita?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection