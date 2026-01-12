{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Galeria')

@section('content')
    @include('beauty.partials.header')
    @include('beauty.scrollGaleriaHero-module')

    @include('beauty.galeria')
    @include('beauty.partials.footer')
    

@endsection
