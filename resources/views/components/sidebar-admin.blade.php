<div class="space-y-2">
    <h2 class="text-lg font-semibold text-gray-700 mb-4 font-montas">Administración</h2>
    
    <a href="{{ url('/admin/home') }}" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 rounded-lg border border-blue-200">
        <i class="fas fa-chart-line mr-3 text-blue-600"></i>
        <span class="font-medium">Dashboard</span>
    </a>
    
    <a href="{{ url('/admin/servicios') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-scissors mr-3 text-purple-600"></i>
        <span>Servicios</span>
    </a>
    
    <a href="{{ url('/admin/citas') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-calendar-alt mr-3 text-green-600"></i>
        <span>Citas</span>
    </a>
    
    <a href="{{ url('/admin/clientes') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-users mr-3 text-orange-600"></i>
        <span>Clientes</span>
    </a>
    
    <a href="{{ url('/admin/empleados') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-user-check mr-3 text-red-600"></i>
        <span>Empleados</span>
    </a>

    <!-- Logout -->
    <div class="pt-4 mt-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-sign-out-alt mr-3 text-gray-600"></i>
                <span>Cerrar Sesión</span>
            </button>
        </form>
    </div>
</div>