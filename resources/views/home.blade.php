{{-- resources/views/nosotros.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/beauty/people.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/homehero.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/imagenes.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/resenas.css') }}">
  <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css">
@endpush

@section('content')
  @include('beauty.partials.whatsapp-icon')
  @include('beauty.partials.header')

  @include('beauty.home.homehero')
  @include('beauty.home.people')
  @include('beauty.home.app-metrics')
  @include('beauty.home.servicioshome')
  @include('beauty.home.resenas')
  @include('beauty.home.donde-encontrarnos')

  @include('beauty.partials.footer')
@endsection

@push('scripts')
  {{-- Lenis smooth scroll (antes que GSAP para que quede registrado) --}}
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>

  {{-- GSAP + ScrollTrigger --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  {{-- Scripts de secciones específicas --}}
  <script src="{{ asset('js/beauty/homehero.js') }}"></script>
  <script src="{{ asset('js/beauty/people.js') }}"></script>
  <script src="{{ asset('js/beauty/imagenes.js') }}"></script>

  {{-- Animaciones generales + Lenis (va al final para que todo el DOM esté listo) --}}
  <script src="{{ asset('js/beauty/home-animations.js') }}" defer></script>
@endpush
