@extends('layouts.app')

@section('title', 'Crear Cita')
@section('page-title', 'Crear Cita')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/agendarcita.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin-citas-booking.css') }}">
@endpush

@section('content')
  <div id="adminBookingRoot">
    @include('admin.citas._form', [
      'mode'         => 'create',
      'action'       => route('admin.citas.store'),
      'cita'         => null,
      'clientes'     => $clientes,
      'servicios'    => $servicios,
      'categorias'   => $categorias,
      'fechaPrefill' => $fechaPrefill ?? request('date'),
    ])
  </div>
@endsection
