{{-- resources/views/nosotros.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/beauty/people.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/homehero.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/imagenes.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/reseñas.css') }}">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@section('content')
  @include('beauty.partials.whatsapp-icon')
  @include('beauty.partials.header')

  @include('beauty.homehero')
  @include('beauty.people')
  @include('beauty.app-metrics')
  @include('beauty.imagenes')
  @include('beauty.donde-encontrarnos')
  @include('beauty.resenas')

  @include('beauty.partials.footer')
@endsection

@push('scripts')
  {{-- GSAP + ScrollTrigger (si no lo tienes ya en tu layout) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <script src="{{ asset('js/beauty/people.js') }}"></script>
  <script src="{{ asset('js/beauty/homehero.js') }}"></script>

  {{-- AOS --}}
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (!window.AOS) return;
      AOS.init({
        duration: 650,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80
      });
    });
  </script>
@endpush
