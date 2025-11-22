@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Cita #{{ $cita->id_cita }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle"></i> Errores de validación:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.citas.update', $cita) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_cliente" class="form-label">
                                        <i class="fas fa-user"></i> Cliente *
                                    </label>
                                    <select name="id_cliente" id="id_cliente" class="form-select" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" 
                                                    {{ (old('id_cliente', $cita->id_cliente) == $cliente->id) ? 'selected' : '' }}>
                                                {{ $cliente->name }} - {{ $cliente->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_servicio" class="form-label">
                                        <i class="fas fa-cut"></i> Servicio *
                                    </label>
                                    <select name="id_servicio" id="id_servicio" class="form-select" required>
                                        <option value="">Seleccione un servicio</option>
                                        @foreach($servicios as $servicio)
                                            <option value="{{ $servicio->id_servicio }}" 
                                                    {{ (old('id_servicio', $cita->id_servicio) == $servicio->id_servicio) ? 'selected' : '' }}>
                                                {{ $servicio->nombre_servicio }} - ${{ number_format($servicio->precio, 2) }} ({{ $servicio->duracion_minutos }} min)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_cita" class="form-label">
                                        <i class="fas fa-calendar"></i> Fecha *
                                    </label>
                                    <input type="date" name="fecha_cita" id="fecha_cita" class="form-control" 
                                           value="{{ old('fecha_cita', $cita->fecha_cita->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hora_cita" class="form-label">
                                        <i class="fas fa-clock"></i> Hora *
                                    </label>
                                    <input type="time" name="hora_cita" id="hora_cita" class="form-control" 
                                           value="{{ old('hora_cita', $cita->hora_cita) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_empleado" class="form-label">
                                        <i class="fas fa-user-tie"></i> Empleado
                                    </label>
                                    <select name="id_empleado" id="id_empleado" class="form-select">
                                        <option value="">Sin asignar</option>
                                        @foreach($empleados as $empleado)
                                            <option value="{{ $empleado->id_empleado }}" 
                                                    {{ (old('id_empleado', $cita->id_empleado) == $empleado->id_empleado) ? 'selected' : '' }}>
                                                {{ $empleado->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="estado_cita" class="form-label">
                                <i class="fas fa-info-circle"></i> Estado *
                            </label>
                            <select name="estado_cita" id="estado_cita" class="form-select" required>
                                <option value="pendiente" {{ old('estado_cita', $cita->estado_cita) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmada" {{ old('estado_cita', $cita->estado_cita) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="cancelada" {{ old('estado_cita', $cita->estado_cita) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="completada" {{ old('estado_cita', $cita->estado_cita) == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">
                                <i class="fas fa-comment"></i> Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                                      placeholder="Notas adicionales sobre la cita...">{{ old('observaciones', $cita->observaciones) }}</textarea>
                        </div>

                        @if($cita->synced_with_google)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Esta cita está sincronizada con Google Calendar. Los cambios se actualizarán automáticamente.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.citas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection