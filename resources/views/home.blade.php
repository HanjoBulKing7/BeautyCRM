{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')

@section('content')
    @include('beauty.partials.header')
    @include('beauty.scrollNormal-module')
    @include('beauty.principal')

    @include('beauty.scrollHorizontal-module')
    @include('beauty.partials.footer')
    

@endsection