<div class="space-y-2">
    <h2 class="text-lg font-semibold text-gray-700 mb-4 font-montas">Mi Cuenta</h2>
    
    <!-- Inicio Dashboard -->
    <a href="{{ url('/home') }}" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 rounded-lg border border-blue-200">
        <i class="fas fa-home mr-3 text-blue-600"></i>
        <span class="font-medium">Inicio Dashboard</span>
    </a>
    
    <!-- Servicios -->
    <a href="{{ url('/servicio') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-spa mr-3 text-purple-600"></i>
        <span>Servicios</span>
    </a>
    
    <!-- Reservar Cita -->
    <a href="{{ url('/reserva') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-calendar-check mr-3 text-green-600"></i>
        <span>Reservar Cita</span>
    </a>
    
    <!-- Sucursal -->
    <a href="{{ url('/sucursal') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-map-marker-alt mr-3 text-orange-600"></i>
        <span>Sucursal</span>
    </a>

    <!-- Portafolio (Galería) -->
    <a href="#galeria" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-images mr-3 text-pink-600"></i>
        <span>Portafolio</span>
    </a>
    
    <!-- Testimonios -->
    <a href="#testimonios" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-comments mr-3 text-teal-600"></i>
        <span>Testimonios</span>
    </a>

    <!-- Mi Perfil -->
    <a href="{{ url('/perfil') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-user mr-3 text-red-600"></i>
        <span>Mi Perfil</span>
    </a>

    <!-- Separador -->
    <div class="pt-4 mt-4 border-t border-gray-200">
        <h3 class="text-md font-semibold text-gray-600 mb-3">Acciones Rápidas</h3>
        
        <!-- WhatsApp -->
        <a href="https://wa.me/524494049194" target="_blank" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition mb-2">
            <i class="fab fa-whatsapp mr-3 text-green-500"></i>
            <span>Contactar por WhatsApp</span>
        </a>
        
        <!-- Agenda tu cita (destacado) 
        <a href="{{ url('/reserva') }}" class="flex items-center px-4 py-3 bg-rose-50 text-rose-700 rounded-lg border border-rose-200 hover:bg-rose-100 transition">
            <i class="fas fa-calendar-plus mr-3 text-rose-600"></i>
            <span class="font-medium">Agendar Cita Rápida</span>
        </a>
        -->
    </div>

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