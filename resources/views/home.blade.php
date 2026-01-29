{{-- resources/views/nosotros.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/beauty/people.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/homehero.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')
    @include('beauty.homehero')
    @include('beauty.people')
    @include('beauty.about-salon')
    @include('beauty.donde-encontrarnos')


    @include('beauty.partials.footer')
    

@endsection

@push('scripts')
  {{-- GSAP + ScrollTrigger (si no lo tienes ya en tu layout) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <script src="{{ asset('js/beauty/people.js') }}"></script>
  <script src="{{ asset('js/beauty/homehero.js') }}"></script>
@endpush