@extends('layouts.app') 

@section('title', 'Reportes - BeautyCRM')

@section('content')
<div class="mb-6 border-b border-gray-200">
    <nav class="flex -mb-px">
        <!-- Pestañas existentes -->
        <a href="{{ route('admin.reportes.index') }}" 
           class="@if(request('tab') == null || request('tab') == 'dashboard') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            📊 Dashboard
        </a>
        
        <a href="{{ route('admin.reportes.index', ['tab' => 'servicios']) }}" 
           class="@if(request('tab') == 'servicios') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            ✂️ Servicios
        </a>
        
        <a href="{{ route('admin.reportes.index', ['tab' => 'productividad']) }}" 
           class="@if(request('tab') == 'productividad') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            ⚡ Productividad
        </a>
        
        <a href="{{ route('admin.reportes.index', ['tab' => 'asistencia']) }}" 
           class="@if(request('tab') == 'asistencia') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            👥 Asistencia
        </a>
        
        <a href="{{ route('admin.reportes.index', ['tab' => 'citas']) }}" 
           class="@if(request('tab') == 'citas') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            📅 Citas
        </a>
        
        <!-- ✅ NUEVA PESTAÑA DE VENTAS -->
        <a href="{{ route('admin.reportes.index', ['tab' => 'ventas']) }}" 
           class="@if(request('tab') == 'ventas') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif py-4 px-6 text-center border-b-2 font-medium text-sm">
            💰 Ventas
        </a>
    </nav>
</div>