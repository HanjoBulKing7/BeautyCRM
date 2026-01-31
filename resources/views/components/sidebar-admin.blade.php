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

<!-- Servicios -->
<li>
    <a href="{{ url('/admin/servicios') }}"
       class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
              {{ request()->is('admin/servicios*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
        <i class="fas fa-scissors mr-3 text-lg"></i>
        <span>Servicios</span>
    </a>
</li>

<!-- Categorías de Servicios -->
<li>
    <a href="{{ url('/admin/categoriaservicios') }}"
       class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
              {{ request()->is('admin/categoriaservicios*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
        <i class="fas fa-tags mr-3 text-lg"></i>
        <span>Categorías</span>
    </a>
</li>

<!-- Productos -->
<li>
    <a href="{{ url('/admin/productos') }}"
       class="flex items-center p-3 rounded-xl text-[15px] font-medium transition-all duration-300
              {{ request()->is('admin/productos*') ? 'bg-yellow-100 text-gray-900 shadow' : 'text-gray-700 hover:bg-gray-50 hover:shadow' }}">
        <i class="fas fa-boxes-stacked mr-3 text-lg"></i>
        <span>Productos</span>
    </a>
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
