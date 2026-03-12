{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Galeria')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/galeria-pages.css') }}">
  <link rel="stylesheet" href="{{ asset('css/beauty/homehero.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsapp-icon')
    @include('beauty.partials.header')
    @include('beauty.homehero')
    @include('beauty.scrollHorizontal-module')
    @include('beauty.Galeria')

    
    @include('beauty.partials.footer')
    

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

  <script src="{{ asset('js/beauty/homehero.js') }}"></script>
  <script src="{{ asset('js/galeria-pages.js') }}" defer></script>
@endpush
