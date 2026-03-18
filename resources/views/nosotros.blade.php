{{-- resources/views/nosotros.blade.php --}}
@extends('layouts.website')

@section('title', 'Beauty Bonita - Nosotros')

@section('content')

    @include('beauty.partials.whatsapp-icon')
    @include('beauty.partials.header')
    @include('beauty.nosotros.scrollNormal-module')
    @include('beauty.nosotros.principal')
    @include('beauty.partials.footer')
    

@endsection