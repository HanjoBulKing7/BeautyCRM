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
  {{-- Lenis (smooth scroll) --}}
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>

  {{-- GSAP + plugins --}}
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

  {{-- 1. Inicializa Lenis + anima todas las secciones del home --}}
  <script src="{{ asset('js/home-animations.js') }}"></script>

  {{-- 2. Hero pin/zoom --}}
  <script src="{{ asset('js/beauty/homehero.js') }}"></script>

  {{-- 3. People section reveals --}}
  <script src="{{ asset('js/beauty/people.js') }}"></script>

  {{-- 4. Gallery carousel dots (mobile) --}}
  <script src="{{ asset('js/beauty/imagenes.js') }}"></script>
@endpush
