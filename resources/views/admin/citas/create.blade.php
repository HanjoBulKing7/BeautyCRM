@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-xl font-semibold mb-4">Crear Cita</h2>

        @include('admin.citas._form', [
            'mode'   => 'create',
            'action' => route('admin.citas.store'),
            'clientes' => $clientes,
            'servicios' => $servicios,
            'empleados' => $empleados,
            'fechaPrefill' => $fechaPrefill, 
            'serviciosForJs' => $serviciosForJs, 
        ])
    </div>
@endsection
