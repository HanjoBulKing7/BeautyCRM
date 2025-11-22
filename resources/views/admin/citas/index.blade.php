@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2><i class="fas fa-calendar-alt"></i> Gestión de Citas</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.google.status') }}" class="btn btn-outline-primary me-2">
                <i class="fab fa-google"></i> Google Calendar
            </a>
            <a href="{{ route('admin.citas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Cita
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Empleado</th>
                            <th>Estado</th>
                            <th>Google</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $cita)
                            <tr>
                                <td><strong>#{{ $cita->id_cita }}</strong></td>
                                <td>
                                    <i class="fas fa-user text-primary"></i> {{ $cita->cliente->name }}
                                    <br><small class="text-muted">{{ $cita->cliente->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $cita->servicio->nombre_servicio }}</strong>
                                    <br><small class="text-muted">{{ $cita->servicio->duracion_minutos }} min - ${{ number_format($cita->servicio->precio, 2) }}</small>
                                </td>
                                <td>{{ $cita->fecha_cita->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($cita->hora_cita)->format('h:i A') }}</td>
                                <td>
                                    @if($cita->empleado)
                                        <i class="fas fa-user-tie"></i> {{ $cita->empleado->nombre }}
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $cita->estado_cita === 'completada' ? 'success' : 
                                        ($cita->estado_cita === 'confirmada' ? 'primary' : 
                                        ($cita->estado_cita === 'cancelada' ? 'danger' : 'warning')) 
                                    }}">
                                        {{ ucfirst($cita->estado_cita) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($cita->synced_with_google)
                                        <i class="fas fa-check-circle text-success" title="Sincronizado con Google Calendar"></i>
                                    @else
                                        <i class="fas fa-times-circle text-muted" title="No sincronizado"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.citas.show', $cita) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.citas.edit', $cita) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.citas.destroy', $cita) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('¿Eliminar esta cita?')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay citas registradas</p>
                                    <a href="{{ route('admin.citas.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear primera cita
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($citas->hasPages())
                <div class="mt-3">
                    {{ $citas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection