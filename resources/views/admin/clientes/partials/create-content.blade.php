<div class="container mx-auto px-4 py-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border"
                    style="
                        background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                        border-color: rgba(201,162,74,.22);
                        box-shadow: 0 10px 22px rgba(201,162,74,.12);
                        color: rgba(17,24,39,.90);
                    "
                >
                    <i class="fas fa-user-plus"></i>
                </span>
                Nuevo Cliente
            </h1>
            <p class="text-gray-600 mt-1">Registra un nuevo cliente para el salón</p>
        </div>

        <a href="{{ route('admin.clientes.index', ['modal' => 1]) }}"
            data-bb-modal="1"
            data-bb-title="Clientes"
            data-bb-url="{{ route('admin.clientes.index', ['modal' => 1]) }}"
            class="...">
            <i class="fas fa-arrow-left" style="color: rgba(201,162,74,.95)"></i>
            Volver
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">

        <!-- Top bar (antes rosa) => Glass dorado estilo dashboard -->
        <div
            class="px-6 py-4"
            style="
                background: linear-gradient(135deg, rgba(201,162,74,.14), rgba(255,255,255,.78));
                border-bottom: 1px solid rgba(201,162,74,.18);
            "
        >
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-id-card" style="color: rgba(201,162,74,.92)"></i>
                Información del cliente
            </h2>
            <p class="text-sm text-gray-600 mt-1">Completa los campos obligatorios (*)</p>
        </div>

        <div class="p-6">
                <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="modal" value="1">

                    @include('admin.clientes._form', ['cliente' => null])

                <div class="flex flex-col sm:flex-row gap-3 pt-2">

                    <!-- Botón Guardar (dorado tipo dashboard) -->
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold transition
                                   focus:outline-none"
                            style="
                                background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                                border: 1px solid rgba(201,162,74,.35);
                                box-shadow: 0 10px 22px rgba(201,162,74,.18);
                                color: #111827;
                            "
                            onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
                            onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
                    >
                        <i class="fas fa-save" style="color: rgba(17,24,39,.90)"></i>
                        Guardar Cliente
                    </button>

                    <a href="{{ route('admin.clientes.index', ['modal' => 1]) }}"
                        data-bb-modal="1"
                        data-bb-title="Clientes"
                        data-bb-url="{{ route('admin.clientes.index', ['modal' => 1]) }}"
                        class="...">
                        <i class="fas fa-times" style="color: rgba(17,24,39,.70)"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>