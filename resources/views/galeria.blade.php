{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Galeria')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/galeria-pages.css') }}">
@endpush

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')
    @include('beauty.scrollGaleriaHero-module')
    @include('beauty.scrollHorizontal-module')
    @include('beauty.galeria')

    
    @include('beauty.partials.footer')
    

@endsection
@push('scripts')
  <script src="{{ asset('js/galeria-pages.js') }}" defer></script>
@endpush
