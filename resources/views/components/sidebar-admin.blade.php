<ul class="space-y-3">
    <!-- Dashboard Admin -->
    <li>
        <a href="{{ url('/admin/home') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('admin/home') ? 'bg-blue-500 bg-opacity-80 font-semibold' : 'hover:bg-blue-400 hover:bg-opacity-30' }}">
            <i class="fas fa-chart-line mr-4 text-xl transition-transform duration-300"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Servicios -->
    <li>
        <a href="{{ url('/admin/servicios') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('admin/servicios*') ? 'bg-purple-500 bg-opacity-80 font-semibold' : 'hover:bg-purple-400 hover:bg-opacity-30' }}">
            <i class="fas fa-scissors mr-4 text-xl transition-transform duration-300"></i>
            <span>Servicios</span>
        </a>
    </li>

    <!-- Citas -->
    <li>
        <a href="{{ url('/admin/citas') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('admin/citas*') ? 'bg-green-500 bg-opacity-80 font-semibold' : 'hover:bg-green-400 hover:bg-opacity-30' }}">
            <i class="fas fa-calendar-alt mr-4 text-xl transition-transform duration-300"></i>
            <span>Citas</span>
        </a>
    </li>

    <!-- Clientes -->
    <li>
        <a href="{{ url('/admin/clientes') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('admin/clientes*') ? 'bg-orange-500 bg-opacity-80 font-semibold' : 'hover:bg-orange-400 hover:bg-opacity-30' }}">
            <i class="fas fa-users mr-4 text-xl transition-transform duration-300"></i>
            <span>Clientes</span>
        </a>
    </li>

    <!-- Empleados -->
    <li>
        <a href="{{ url('/admin/empleados') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('admin/empleados*') ? 'bg-red-500 bg-opacity-80 font-semibold' : 'hover:bg-red-400 hover:bg-opacity-30' }}">
            <i class="fas fa-user-check mr-4 text-xl transition-transform duration-300"></i>
            <span>Empleados</span>
        </a>
    </li>
</ul>