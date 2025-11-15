<ul class="space-y-3">
    <!-- Inicio Dashboard -->
    <li>
        <a href="{{ url('/home') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('home') ? 'bg-blue-500 bg-opacity-80 font-semibold' : 'hover:bg-blue-400 hover:bg-opacity-30' }}">
            <i class="fas fa-home mr-4 text-xl transition-transform duration-300"></i>
            <span>Inicio Dashboard</span>
        </a>
    </li>

    <!-- Servicios -->
    <li>
        <a href="{{ url('/servicio') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('servicio*') ? 'bg-purple-500 bg-opacity-80 font-semibold' : 'hover:bg-purple-400 hover:bg-opacity-30' }}">
            <i class="fas fa-spa mr-4 text-xl transition-transform duration-300"></i>
            <span>Servicios</span>
        </a>
    </li>

    <!-- Reservar Cita -->
    <li>
        <a href="{{ url('/reserva') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('reserva*') ? 'bg-green-500 bg-opacity-80 font-semibold' : 'hover:bg-green-400 hover:bg-opacity-30' }}">
            <i class="fas fa-calendar-check mr-4 text-xl transition-transform duration-300"></i>
            <span>Reservar Cita</span>
        </a>
    </li>

    <!-- Sucursal -->
    <li>
        <a href="{{ url('/sucursal') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('sucursal*') ? 'bg-orange-500 bg-opacity-80 font-semibold' : 'hover:bg-orange-400 hover:bg-opacity-30' }}">
            <i class="fas fa-map-marker-alt mr-4 text-xl transition-transform duration-300"></i>
            <span>Sucursal</span>
        </a>
    </li>

    <!-- Portafolio (Galería) -->
    <li>
        <a href="#galeria"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('galeria*') ? 'bg-pink-500 bg-opacity-80 font-semibold' : 'hover:bg-pink-400 hover:bg-opacity-30' }}">
            <i class="fas fa-images mr-4 text-xl transition-transform duration-300"></i>
            <span>Portafolio</span>
        </a>
    </li>
    
    <!-- Testimonios -->
    <li>
        <a href="#testimonios"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('testimonios*') ? 'bg-teal-500 bg-opacity-80 font-semibold' : 'hover:bg-teal-400 hover:bg-opacity-30' }}">
            <i class="fas fa-comments mr-4 text-xl transition-transform duration-300"></i>
            <span>Testimonios</span>
        </a>
    </li>

    <!-- Mi Perfil -->
    <li>
        <a href="{{ url('/perfil') }}"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out
                  {{ request()->is('perfil*') ? 'bg-red-500 bg-opacity-80 font-semibold' : 'hover:bg-red-400 hover:bg-opacity-30' }}">
            <i class="fas fa-user mr-4 text-xl transition-transform duration-300"></i>
            <span>Mi Perfil</span>
        </a>
    </li>

    <!-- Separador para Acciones Rápidas -->
    <li class="pt-4 mt-4 border-t border-gray-500">
        <h3 class="text-md font-semibold mb-3 px-3">Acciones Rápidas</h3>
        
        <!-- WhatsApp -->
        <a href="https://wa.me/524494049194" target="_blank"
           class="flex items-center p-3 rounded-lg text-lg transition-all duration-300 ease-in-out hover:bg-green-500 hover:bg-opacity-30">
            <i class="fab fa-whatsapp mr-4 text-xl transition-transform duration-300"></i>
            <span>Contactar por WhatsApp</span>
        </a>
    </li>
</ul>