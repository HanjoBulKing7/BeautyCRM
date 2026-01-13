@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-xl font-semibold mb-4">Editar Cita</h2>

        @include('admin.citas._form', [
            'mode'   => 'edit',
            'action' => route('admin.citas.update', $cita),
            'cita'   => $cita,
            'clientes' => $clientes,
            'servicios' => $servicios,
            'empleados' => $empleados,
        ])
    </div>
@endsection
