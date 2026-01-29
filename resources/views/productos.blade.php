@extends('layouts.website')

@section('title', 'Beauty Bonita - Productos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/beauty/productos-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/productos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/menu-productos.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')
    @include('beauty.scrollGaleriaHero-module')

    @include('beauty.productos.menu-productos')
    @include('beauty.productos.categorias')

    @include('beauty.partials.footer')
@endsection
