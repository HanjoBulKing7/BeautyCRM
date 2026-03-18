{{-- resources/views/servicio.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Servicio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/beauty/servicios-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/menu-servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/nuevo-servicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beauty/homehero.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsapp-icon')
    @include('beauty.partials.header')
    @include('beauty.servicios.heroservicios')

    @include('beauty.servicios.menu-servicios')
    @include('beauty.servicios.mas-solicitados')
    @include('beauty.servicios.nuevo-servicio')
    @include('beauty.servicios.categorias')


    @include('beauty.partials.footer')
@endsection

@push('scripts')
  {{-- GSAP + ScrollTrigger (si no lo tienes ya en tu layout) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <script src="{{ asset('js/beauty/homehero.js') }}"></script>
@endpush