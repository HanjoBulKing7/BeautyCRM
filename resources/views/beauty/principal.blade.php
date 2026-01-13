
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
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Maquillaje Profesional" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">01</span>
                                    Maquillaje Profesional
                                </h5>
                                <h2 class="Normal-content-title">Realza tu Belleza Natural</h2>
                                <p class="Normal-content-copy">
                                    Resaltamos tu belleza con maquillaje profesional diseñado para tu estilo,
                                    ocasión y rasgos. Utilizamos productos de alta calidad y técnicas actuales
                                    para lograr un acabado impecable, elegante y duradero. Perfecto para eventos,
                                    sesiones fotográficas o un look sofisticado para tu día a día.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
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
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Peinados Profesionales" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">02</span>
                                    Estilo y Color
                                </h5>
                                <h2 class="Normal-content-title">
                                    <span>Peinados Profesionales</span>
                                    <span>y Color Personalizado</span>
                                </h2>
                                <p class="Normal-content-copy">
                                    Transformamos tu estilo con peinados diseñados para realzar tus rasgos y tu
                                    personalidad, complementados con técnicas de coloración profesional que aportan
                                    luminosidad, dimensión y un acabado totalmente personalizado. Desde tonos
                                    naturales hasta cambios atrevidos, trabajamos con productos de alta calidad
                                    para lograr un look vibrante, elegante y hecho a tu medida.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
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
                                <img src="{{ asset('images/Faciales.jpg') }}" alt="Tratamientos Capilares" />
                            </div>
                            <div class="Normal-content-content">
                                <h5 class="Normal-content-subtitle">
                                    <span class="counter">03</span>
                                    Cuidado Avanzado
                                </h5>
                                <h2 class="Normal-content-title">
                                    <span>Tratamientos de</span>
                                    <span>Cabello Saludable</span>
                                </h2>
                                <p class="Normal-content-copy">
                                    Nuestros tratamientos capilares están diseñados para restaurar, fortalecer y
                                    revitalizar tu cabello desde la raíz hasta las puntas. Utilizamos productos de
                                    alta calidad que hidratan profundamente, reparan el daño y mejoran la textura,
                                    dejándolo más suave, brillante y manejable. Ideal para quienes buscan renovar
                                    la salud de su cabello y lucir un acabado elegante y lleno de vida.
                                </p>
                                <a href="#" class="Normal-content-action">
                                    Ver más
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