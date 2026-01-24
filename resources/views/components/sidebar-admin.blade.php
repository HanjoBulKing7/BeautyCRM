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
           data-bb-modal
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
           data-bb-modal
           data-bb-title="Citas Completadas"
           data-bb-url="{{ url('/admin/ventas') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/ventas*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-cash-register mr-3 text-lg"></i>
            <span>Citas Completadas</span>
        </a>
    </li>

    <!-- Servicios (modal) -->
    <li>
        <a href="{{ url('/admin/servicios') }}"
           data-bb-modal
           data-bb-title="Servicios"
           data-bb-url="{{ url('/admin/servicios') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/servicios*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-scissors mr-3 text-lg"></i>
            <span>Servicios</span>
        </a>
    </li>

    <!-- Clientes (modal) -->
    <li>
        <a href="{{ url('/admin/clientes') }}"
           data-bb-modal
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
           data-bb-modal
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
           data-bb-modal
           data-bb-title="Reportes"
           data-bb-url="{{ url('/admin/reportes') }}?modal=1"
           class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
                  {{ request()->is('admin/reportes*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
            <i class="fas fa-chart-pie mr-3 text-lg"></i>
            <span>Reportes</span>
        </a>
    </li>
</ul>
