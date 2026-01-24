@extends('layouts.app')

@section('page-title', 'Gestión de Empleados')
@section('title', 'Empleados - Salón de Belleza')

@section('content')
  @include('admin.empleados.partials.index-content')
@endsection
