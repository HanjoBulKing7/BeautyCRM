@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Configuración de Google Calendar</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($isConnected)
                        <div class="alert alert-success">
                            <strong>✓ Conectado</strong> - Las citas se sincronizarán automáticamente con Google Calendar
                        </div>

                        <a href="{{ route('admin.google.disconnect') }}" 
                           class="btn btn-danger"
                           onclick="return confirm('¿Desconectar Google Calendar?')">
                            Desconectar Google Calendar
                        </a>
                    @else
                        <div class="alert alert-warning">
                            <strong>No conectado</strong> - Las citas NO se sincronizarán con Google Calendar
                        </div>

                        <p>Conecta tu cuenta de Google Calendar para sincronizar automáticamente todas las citas.</p>

                        <a href="{{ route('admin.google.connect') }}" class="btn btn-primary">
                            <i class="fab fa-google"></i> Conectar con Google Calendar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection