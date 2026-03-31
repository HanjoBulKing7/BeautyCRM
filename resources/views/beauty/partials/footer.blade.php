@push('styles')
        <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

@endpush
    <section class="Normal-footer-section">
        <footer class="Normal-footer">
            <div class="container">
                <div class="Normal-footer-row">
                    <div class="Normal-footer-column Normal-footer-column-logo">
                        <div class="Normal-footer-logo">
                            <a href="{{ url('/home') }}">
                            <img src="{{ asset('iconos/logo.png') }}" alt="Beauty Bonita Logo">
                            </a>
                        </div>
                        <div class="Normal-footer-copy">
                            Descubre tu belleza única & transforma <br>tu estilo con nuestros servicios profesionales!
                        </div>
                        <div class="Normal-footer-copy-rights">© 2026 Beauty Bonita. Todos los derechos reservados.</div>
                    </div>
                    <div class="Normal-footer-column Normal-footer-column-link">
                        <h4 class="Normal-footer-heading">Nuestros Servicios</h4>
                        <ul class="Normal-footer-links-list">
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/servicio#servicios-categorias') }}">Maquillaje Profesional</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/servicio#servicios-categorias') }}">Corte y Peinado</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/servicio#servicios-categorias') }}">Coloración</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/servicio#servicios-categorias') }}">Tratamientos Capilares</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/servicio#servicios-categorias') }}">Manicure y Pedicure</a>
                            </li>
                        </ul>
                    </div>
                    <div class="Normal-footer-column Normal-footer-column-link">
                        <h4 class="Normal-footer-heading">Contacto</h4>
                        <ul class="Normal-footer-links-list">
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/agendar-cita') }}">Reservar Cita</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/agendar-cita#bbCalendar') }}">Horarios</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/home#find-us') }}">Ubicación</a>
                            </li>
                            <li class="Normal-footer-links-item">
                                <a href="{{ url('/home#servicios-home') }}">Promociones</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </section>