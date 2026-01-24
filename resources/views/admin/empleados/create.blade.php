@extends('layouts.app')
@section('title','Crear Empleado - Salón de Belleza')

@section('content')
  @include('admin.empleados.partials.create-content', ['empleado' => $empleado])
@endsection
