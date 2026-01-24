<ul class="space-y-2">
    {{-- ✅ Tip: aquí ya NO usamos bg-blue-500/bg-red-500 etc.
       Usamos bg-*-100 (para que tu CSS “gold active” lo convierta en dorado elegante)
       y hover suave (glass) --}}
    
    <!-- Dashboard Admin -->
    <li>
        <a href="{{ url('/admin/home') }}"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/home') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-chart-line mr-3 text-lg"></i>
            <span>Dashboard</span>
        </a>
    </li>

    
    <!-- Citas -->
    <li>
        <a href="{{ url('/admin/citas') }}"
        class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
        {{ request()->is('admin/citas*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
        <i class="fas fa-calendar-alt mr-3 text-lg"></i>
        <span>Citas</span>
    </a>
</li>

<!-- Ventas -->
<li>
    <a href="{{ url('/admin/ventas') }}"
    class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
    {{ request()->is('admin/ventas*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
    <i class="fas fa-cash-register mr-3 text-lg"></i>
    <span>Citas Completadas</span>
</a>
</li>

{{-- =====================
   Servicios (submenu limpio)
===================== --}}
@php
  $openServicios = request()->is('admin/servicios*')
             || request()->is('admin/categoriaservicios*')
             || request()->is('admin/productos*');

@endphp
<!-- Servicios (Servicios + Categorías + Productos) -->
<li>
    <a href="#"
       data-submenu="submenu-servicios"
       aria-expanded="{{ $openServicios ? 'true' : 'false' }}"
       class="nav-group-toggle {{ $openServicios ? 'bg-yellow-100 text-yellow-600 font-semibold active' : 'text-gray-700' }}">
        <span class="flex items-center">
            <i class="fas fa-scissors text-yellow-500"></i>
            <span>Servicios</span>
        </span>
        <i class="fas fa-chevron-right nav-chevron {{ $openServicios ? 'open' : '' }}"></i>
    </a>

    <ul id="submenu-servicios" class="nav-submenu {{ $openServicios ? '' : 'hidden' }}">
        <li>
            <a href="{{ url('/admin/servicios') }}"
               class="{{ request()->is('admin/servicios*') ? 'bg-yellow-100 text-yellow-600 font-semibold active' : 'text-gray-700' }}">
                <i class="fas fa-list text-yellow-500"></i>
                <span>Servicios</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/admin/categoriaservicios') }}"
               class="{{ request()->is('admin/categoriaservicios*') ? 'bg-yellow-100 text-yellow-600 font-semibold active' : 'text-gray-700' }}">
                <i class="fas fa-layer-group text-yellow-500"></i>
                <span>Categorías</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/admin/productos') }}"
               class="{{ request()->is('admin/productos*') ? 'bg-yellow-100 text-yellow-600 font-semibold active' : 'text-gray-700' }}">
                <i class="fas fa-box text-yellow-500"></i>
                <span>Productos</span>
            </a>
        </li>
    </ul>
</li>


<!-- Clientes -->
<li>
        <a href="{{ url('/admin/clientes') }}"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/clientes*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span>Clientes</span>
        </a>
    </li>

    <!-- Empleados -->
    <li>
        <a href="{{ url('/admin/empleados') }}"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/empleados*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-user-check mr-3 text-lg"></i>
            <span>Empleados</span>
        </a>
    </li>

    <!-- Reportes -->
    <li>
        <a href="{{ url('/admin/reportes') }}"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/reportes*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-chart-pie mr-3 text-lg"></i>
            <span>Reportes</span>
        </a>
    </li>
</ul>
