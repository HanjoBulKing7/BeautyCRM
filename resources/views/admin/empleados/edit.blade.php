@extends('layouts.app')
@section('title','Editar Empleado - Salón de Belleza')

@section('content')
  @include('admin.empleados.partials.edit-content', ['empleado' => $empleado])
@endsection
