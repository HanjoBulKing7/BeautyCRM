<ul class="space-y-2">
    {{-- ✅ Tip: aquí ya NO usamos bg-blue-500/bg-red-500 etc.
       Usamos bg-*-100 (para que tu CSS “gold active” lo convierta en dorado elegante)
       y hover suave (glass) --}}

    <!-- Dashboard Admin (normal) -->
    <li>
        <a href="{{ url('/admin/home') }}"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/home') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-chart-line mr-3 text-lg"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Citas (modal) -->
    <li>
        <a href="{{ url('/admin/citas') }}"
           data-bb-modal="1"
           data-bb-title="Citas"
           data-bb-url="{{ url('/admin/citas') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/citas*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-calendar-alt mr-3 text-lg"></i>
            <span>Citas</span>
        </a>
    </li>

    <!-- Ventas (Citas Completadas) (modal) -->
    <li>
        <a href="{{ url('/admin/ventas') }}"
           data-bb-modal="1"
           data-bb-title="Citas Completadas"
           data-bb-url="{{ url('/admin/ventas') }}?modal=1"
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
        $openServicios =
            request()->is('admin/servicios*')
            || request()->is('admin/categoriaservicios*')
            || request()->is('admin/productos*');
    @endphp

    <!-- Servicios (Servicios + Categorías + Productos) -->
    <li>
        <a href="#"
           data-submenu="submenu-servicios"
           aria-expanded="{{ $openServicios ? 'true' : 'false' }}"
           class="nav-group-toggle flex items-center justify-between p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ $openServicios ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <span class="flex items-center gap-3">
                <i class="fas fa-scissors text-lg"></i>
                <span>Servicios</span>
            </span>

            <i class="fas fa-chevron-right nav-chevron {{ $openServicios ? 'open' : '' }}"></i>
        </a>

        <ul id="submenu-servicios" class="nav-submenu mt-2 space-y-1 {{ $openServicios ? '' : 'hidden' }}">
            <li>
                <a href="{{ url('/admin/servicios') }}"
                   data-bb-modal="1"
                   data-bb-title="Servicios"
                   data-bb-url="{{ url('/admin/servicios') }}?modal=1"
                   class="flex items-center gap-3 p-3 rounded-xl text-[14px] font-medium transition-all duration-300
                          {{ request()->is('admin/servicios*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
                    <i class="fas fa-list"></i>
                    <span>Servicios</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/admin/categoriaservicios') }}"
                   data-bb-modal="1"
                   data-bb-title="Categorías"
                   data-bb-url="{{ url('/admin/categoriaservicios') }}?modal=1"
                   class="flex items-center gap-3 p-3 rounded-xl text-[14px] font-medium transition-all duration-300
                          {{ request()->is('admin/categoriaservicios*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Categorías</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/admin/productos') }}"
                   data-bb-modal="1"
                   data-bb-title="Productos"
                   data-bb-url="{{ url('/admin/productos') }}?modal=1"
                   class="flex items-center gap-3 p-3 rounded-xl text-[14px] font-medium transition-all duration-300
                          {{ request()->is('admin/productos*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- Clientes (modal) -->
    <li>
        <a href="{{ url('/admin/clientes') }}"
           data-bb-modal="1"
           data-bb-title="Clientes"
           data-bb-url="{{ url('/admin/clientes') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/clientes*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span>Clientes</span>
        </a>
    </li>

    <!-- Empleados (modal) -->
    <li>
        <a href="{{ url('/admin/empleados') }}"
           data-bb-modal="1"
           data-bb-title="Empleados"
           data-bb-url="{{ url('/admin/empleados') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/empleados*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-user-check mr-3 text-lg"></i>
            <span>Empleados</span>
        </a>
    </li>

    <!-- Reportes (modal) -->
    <li>
        <a href="{{ url('/admin/reportes') }}"
           data-bb-modal="1"
           data-bb-title="Reportes"
           data-bb-url="{{ url('/admin/reportes') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/reportes*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-chart-pie mr-3 text-lg"></i>
            <span>Reportes</span>
        </a>
    </li>
</ul>
