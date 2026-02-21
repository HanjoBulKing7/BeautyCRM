@extends('layouts.app')

@section('title', 'Reportes - BeautyCRM')

@section('content')
@php
  $tab  = $tab ?? request('tab', 'ventas');
  $tipo = $tipo ?? request('tipo', 'diario');
  $fecha = $fecha ?? request('fecha', now()->toDateString());
@endphp

<div class="p-6">


  {{-- Sin tabs: solo ventas --}}
  @php $tab = 'ventas'; @endphp

  {{-- Contenido --}}
  <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">

    {{-- Siempre mostrar ventas --}}

      {{-- Sub-tabs: Diario / Semanal / Mensual --}}
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Reporte de Ventas</h1>
          <p class="text-gray-500 dark:text-gray-400">Consulta el desempeño del negocio por periodo.</p>
        </div>

        {{-- Selector de fecha según tipo --}}
        <div class="flex items-center gap-3">
          @if($tipo === 'diario')
            <input id="fecha-dia" type="date" value="{{ $fecha }}"
                   class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
          @elseif($tipo === 'semanal')
            <input id="fecha-semana" type="date" value="{{ $fecha }}"
                   class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
          @else
            <input id="fecha-mes" type="month" value="{{ \Carbon\Carbon::parse($fecha)->format('Y-m') }}"
                   class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
          @endif

          <button onclick="actualizarReporte()"
                  class="px-4 py-2 rounded-lg bg-amber-500 text-black border-b-2 hover:bg-amber-600">
            Actualizar
          </button>
        </div>
      </div>
      <div class="mb-6 border-b border-gray-200 dark:border-gray-800">
        <nav class="flex gap-6">
          <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'diario','fecha'=>$fecha]) }}"
             class="pb-3 border-b-2 {{ $tipo === 'diario' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Diario
          </a>
          <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'semanal','fecha'=>$fecha]) }}"
             class="pb-3 border-b-2 {{ $tipo === 'semanal' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Semanal
          </a>
          <a href="{{ route('admin.reportes.index', ['tab'=>'ventas','tipo'=>'mensual','fecha'=>$fecha]) }}"
             class="pb-3 border-b-2 {{ $tipo === 'mensual' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Mensual
          </a>
        </nav>
      </div>

      {{-- Render según tipo --}}
      <div class="mt-6">
        @if($tipo === 'diario')
          @include('admin.reportes.diario')
        @elseif($tipo === 'semanal')
          @include('admin.reportes.semanal')
        @else
          @include('admin.reportes.mensual')
        @endif
      </div>

  {{-- Fin ventas --}}
  </div>
</div>

<script>
function actualizarReporte() {
  const tipo = @json($tipo);
  const tab  = @json($tab);

  const url = new URL(window.location.href);

  let fecha = '';
  if (tipo === 'diario') {
    const el = document.getElementById('fecha-dia');
    if (el) fecha = el.value;
  } else if (tipo === 'semanal') {
    const el = document.getElementById('fecha-semana');
    if (el) fecha = el.value;
  } else {
    const el = document.getElementById('fecha-mes');
    if (el) fecha = el.value ? (el.value + '-01') : '';
  }

  url.searchParams.set('tab', tab);
  url.searchParams.set('tipo', tipo);
  if (fecha) url.searchParams.set('fecha', fecha);

  window.location.href = url.toString();
}
</script>
@endsection
