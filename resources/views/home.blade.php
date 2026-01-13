{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')

@section('content')
    @include('beauty.partials.whatsApp-icon')
    @include('beauty.partials.header')
    @include('beauty.scrollNormal-module')
    @include('beauty.principal')
    @include('beauty.about-salon')
    @include('cliente.servicio')
    @include('beauty.donde-encontrarnos')

    @include('beauty.partials.footer')
    

@endsection