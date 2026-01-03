{{-- resources/views/home.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Salon de Belleza')

@section('content')
    @include('beauty.scrollNormal-module')
    @include('beauty.scrollHorizontal-module')
    @include('beauty.scrollLenis-module')
@endsection