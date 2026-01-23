{{-- resources/views/servicio.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Servicio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/beauty/servicios-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/menu-servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/nuevo-servicio.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')x
    @include('beauty.scrollGaleriaHero-module')

    @include('beauty.servicios.menu-servicios')
    @include('beauty.servicios.mas-solicitados')
    @include('beauty.servicios.nuevo-servicio')
    @include('beauty.servicios.categorias')


    @include('beauty.partials.footer')
@endsection

