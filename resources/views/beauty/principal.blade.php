
            <style>
           /* ✅ SOLO LO NECESARIO: que el slider NO bloquee clicks del footer */
.Normal-slider {
  pointer-events: none; /* el nav no captura clicks */
}

/* ✅ pero los links y la barrita SÍ sean clickeables */
.Normal-slider a,
.Normal-slider .Normal-slider-progress,
.Normal-slider .Normal-slider-progress-bar,
.Normal-slider .Normal-slider-list,
.Normal-slider .Normal-slider-list * {
  pointer-events: auto;
}

/* (Opcional mínimo) si quieres asegurar que quede encima pero sin bloquear */
.Normal-slider {
  z-index: 50;
}



            </style>
            <section class="Normal-content-section section mb-4">
                <div class="container">
                    <div class="Normal-content-wrapper" id="section-01">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/Peinado2.png') }}" alt="Maquillaje Profesional" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">01</span>
                                    Promoción de Cumpleaños
                                </h5>
                              <h2 class="Normal-content-title">Promociones</h2>
                                <h5 class="Normal-content-subtitle" style="margin-top: -10px; margin-bottom: 15px;">
                                    Descuento exclusivo por tu cumpleaños
                                </h5>
                                <p class="Normal-content-copy">
                                    ¡Queremos consentirte en tu mes! Celebra con nosotros y disfruta de un <strong>descuento especial en nuestro salón de belleza</strong>. Solo presenta tu identificación y obtén una tarifa preferencial en tu tratamiento favorito: desde peinados y faciales revitalizantes, hasta un servicio completo de uñas. Regálate la experiencia de lujo que mereces.
                                </p>
                                <a href="{{ url('/agendar-cita') }}" class="Normal-content-action">
                                    Agendar mi cita
                                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                    </svg>
                                </a>

                            </div>
                        </div>
                    </div>
                    <div class="Normal-content-wrapper" id="section-02">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/eventos.jpg') }}" alt="Peinados Profesionales" />
                            </div>
                           <div class="Normal-content-content">
                            <h5 class="Normal-content-subtitle">
                                <span class="counter">02</span>
                                Academia y Formación
                            </h5>
                            <h2 class="Normal-content-title">
                                <span>Domina el Arte</span>
                                <span>del Peinado</span>
                            </h2>
                            <p class="Normal-content-copy">
                                Lleva tu pasión al siguiente nivel con nuestro <strong>Curso de Peinado Profesional</strong>. Aprende técnicas modernas, las últimas tendencias y los secretos del estilismo directamente de la mano del experto <strong>Pako Rodríguez</strong>. Perfecciona tus habilidades y prepárate para destacar en el competitivo mundo de la belleza. <br><br>
                                <em>¡Espéralo! Próximas fechas por anunciarse.</em>
                            </p>
                            <a href="{{ url('/contacto') }}" class="Normal-content-action">
                                Solicitar información
                                <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                </svg>
                            </a>
                        </div>
                        </div>
                    </div>
                    <div class="Normal-content-wrapper" id="section-03">
                        <div class="Normal-content-row">
                            <div class="Normal-content-image">
                                <img src="{{ asset('images/servicios/masaje.jpg') }}" alt="Tratamientos Capilares" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">03</span>
                                    Masajes de Alta Tecnología
                                </h5>
                                <h2 class="Normal-content-title">
                                    <span>Reactivá tu Cuerpo,</span>
                                    <span>Renová tu Energía</span>
                                </h2>
                                <p class="Normal-content-copy">
                                    Descubrí una nueva dimensión del bienestar con nuestros masajes corporales de lujo. Utilizamos tecnología de vanguardia como el exclusivo sistema Rollaction, un innovador método de masaje fisio-activo que imita los movimientos profesionales de las manos del terapeuta para actuar en profundidad sobre el tejido muscular, vascular y dérmico. Ideal para tratamientos de abdomen, este sistema reactiva las funciones naturales del organismo, mejorando la circulación, reduciendo la retención de líquidos y remodelando la silueta. Combinamos esta avanzada aparatología con masajes tradicionales personalizados para ofrecerte una experiencia única que equilibra cuerpo y mente, liberando tensiones y devolviéndole la vitalidad a tu piel.
                                </p>
                                <a href="{{ url('/agendar-cita') }}" class="Normal-content-action">
                                    Agendar servicio
                                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 -6.99382e-07L14.59 1.41L20.17 7L-3.93402e-07 7L-3.0598e-07 9L20.17 9L14.58 14.58L16 16L24 8L16 -6.99382e-07Z" fill="#b8860b" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <nav class="Normal-slider">
                <div class="container">
                    <ul class="Normal-slider-list">
                        <li class="Normal-slider-list-item">
                            <a href="#section-00">Inicio</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-01">01</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-02">02</a>
                        </li>
                        <li class="Normal-slider-list-item">
                            <a href="#section-03">03</a>
                        </li>
                    </ul>
                    <div class="Normal-slider-progress">
                        <div class="Normal-slider-progress-bar" style="height: 20%;"></div>
                    </div>
                </div>
            </nav>
        </main>
    </section>