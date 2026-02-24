@extends('layouts.app')

@section('title', 'Editar Cita')
@section('page-title', 'Editar Cita')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/agendarcita.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin-citas-booking.css') }}">
@endpush

@section('content')
  <div id="adminBookingRoot">
    @include('admin.citas._form', [
      'mode'         => 'edit',
      'action'       => route('admin.citas.update', $cita),
      'cita'         => $cita,
      'clientes'     => $clientes,
      'servicios'    => $servicios,
      'categorias'   => $categorias,
      // opcional (no estorba)
      'fechaPrefill' => $cita->fecha_cita ?? null,
    ])
  </div>
@endsection
