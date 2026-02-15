<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <!-- Header -->
    <div
        class="p-6"
        style="
            background: linear-gradient(135deg, rgba(201,162,74,.14), rgba(255,255,255,.78));
            border-bottom: 1px solid rgba(201,162,74,.18);
        "
    >
        <div class="flex items-center gap-3">
            <div
                class="p-3 rounded-full border"
                style="
                    background: linear-gradient(135deg, rgba(201,162,74,.18), rgba(255,255,255,.75));
                    border-color: rgba(201,162,74,.22);
                    box-shadow: 0 10px 22px rgba(201,162,74,.12);
                "
            >
                <i class="fas fa-spa text-xl" style="color: rgba(17,24,39,.90)"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $servicio->nombre_servicio }}</h1>
                <p class="text-gray-600 text-sm">Detalles del servicio</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Imagen del servicio --}}
        @if(!empty($servicio->imagen))
            <div class="flex justify-center">
                <div class="w-[320px] rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <img
                        src="{{ asset('storage/' . $servicio->imagen) }}"
                        alt="Foto del servicio {{ $servicio->nombre_servicio }}"
                        class="w-full h-[180px] object-cover"
                        loading="lazy"
                    >
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Información principal -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-circle-info" style="color: rgba(201,162,74,.92)"></i>
                        Información Básica
                    </h3>

                    <div class="space-y-2 text-gray-700">
                        <p>
                            <span class="font-medium text-gray-800">Categoría:</span>
                            {{ $servicio->categoria->nombre ?? 'No especificada' }}
                        </p>

                        <p>
                            <span class="font-medium text-gray-800">Precio:</span>
                            ${{ number_format($servicio->precio, 2) }}
                        </p>

                        <p>
                            <span class="font-medium text-gray-800">Duración:</span>
                            {{ $servicio->duracion_minutos }} minutos
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-medium text-gray-800">Estado:</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $servicio->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($servicio->estado) }}
                            </span>
                        </p>

                        {{-- ✅ Descuento removido de UI --}}
                    </div>
                </div>
            </div>

            <!-- Descripción y características -->
            <div class="space-y-4">
                @if($servicio->descripcion)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-left" style="color: rgba(201,162,74,.92)"></i>
                            Descripción
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $servicio->descripcion }}</p>
                    </div>
                @endif

                @if($servicio->caracteristicas)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-list" style="color: rgba(201,162,74,.92)"></i>
                            Características
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $servicio->caracteristicas }}</p>
                    </div>
                @endif
            </div>

        </div>

        @php
            $dias = [
                0 => 'Domingo',
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
            ];

            $horariosPorDia = ($servicio->horarios ?? collect())
                ->sortBy([
                    ['dia_semana', 'asc'],
                    ['hora_inicio', 'asc'],
                ])
                ->groupBy('dia_semana');

            $diasConHorario = $horariosPorDia->keys()->sort()->values();
            $fmt = fn($t) => $t ? \Carbon\Carbon::parse($t)->format('H:i') : '';
        @endphp

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock" style="color: rgba(201,162,74,.92)"></i>
                Horarios del Servicio
            </h3>

            @if($diasConHorario->isEmpty())
                <p class="text-gray-500 text-sm">No hay horarios definidos para este servicio.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($diasConHorario as $numDia)
                        @php
                            $rangos   = $horariosPorDia->get($numDia, collect());
                            $labelDia = $dias[$numDia] ?? ('Día ' . $numDia);
                        @endphp

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold text-gray-800">{{ $labelDia }}</span>

                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-800">
                                    {{ $rangos->count() }} rango(s)
                                </span>
                            </div>

                            <ul class="space-y-2">
                                @foreach($rangos as $h)
                                    @php
                                        $inicio = $fmt($h->hora_inicio);
                                        $fin    = $fmt($h->hora_fin);
                                        $cruzaMedianoche = ($inicio && $fin && $fin < $inicio);
                                    @endphp

                                    <li class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                                        <div class="text-sm text-gray-700">
                                            <span class="font-medium">{{ $inicio }}</span>
                                            <span class="text-gray-400 mx-1">—</span>
                                            <span class="font-medium">{{ $fin }}</span>

                                            @if($cruzaMedianoche)
                                                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                                    Cruza medianoche
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">

            <!-- Editar (NORMAL, sin modal) -->
            <a
                href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}"
                class="h-12 px-6 rounded-lg font-semibold inline-flex items-center justify-center gap-2 transition"
                style="
                background: linear-gradient(135deg, var(--bb-gold), var(--bb-gold-2));
                border: 1px solid rgba(201,162,74,.35);
                box-shadow: 0 10px 22px rgba(201,162,74,.18);
                color: #111827;
                "
                onmouseover="this.style.boxShadow='0 16px 30px rgba(201,162,74,.22)'"
                onmouseout="this.style.boxShadow='0 10px 22px rgba(201,162,74,.18)'"
            >
                <i class="fas fa-edit" style="color: rgba(17,24,39,.90)"></i>
                Editar Servicio
            </a>

            <!-- Eliminar -->
            <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}"
                  method="POST"
                  class="m-0 inline-flex">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="h-12 px-6 rounded-lg bg-red-500 hover:bg-red-600 text-white font-semibold
                           inline-flex items-center justify-center gap-2 transition shadow-sm hover:shadow-md"
                    onclick="return confirm('¿Estás seguro de eliminar este servicio?')">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
            </form>
        </div>

    </div>
</div>
