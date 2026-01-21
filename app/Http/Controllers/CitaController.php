<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaConfirmadaMail;
use Illuminate\Support\Facades\Log;
use App\Models\GoogleToken;
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\ServicioHorario;
use Illuminate\Support\Facades\Schema;

class CitaController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    public function horasDisponibles(Request $request)
    {
        $date      = $request->query('date');
        $servicios = $request->query('servicios', []);
        $empleados = $request->query('empleados', []); // opcional

        if (!$date || !is_array($servicios) || count($servicios) === 0) {
            return response()->json([]);
        }

        $fecha = Carbon::parse($date);
        $dow   = $fecha->dayOfWeekIso; // 1..7 (Lun..Dom)

        $ids = collect($servicios)
            ->map(fn($v) => (int)$v)
            ->filter()
            ->unique()
            ->values();

        // 🔥 tus servicios usan PK id_servicio
        $serviciosDb = Servicio::whereIn('id_servicio', $ids)->get()->keyBy('id_servicio');
        if ($serviciosDb->isEmpty()) return response()->json([]);

        // Duración total (ajusta el campo si en tu tabla se llama distinto)
        $totalMin = (int) $ids->sum(fn($id) => (int)($serviciosDb[$id]->duracion_minutos ?? 0));
        if ($totalMin <= 0) $totalMin = 30;

        // ✅ helper robusto: "HH:MM" o "HH:MM:SS" -> minutos del día
        $toMinutes = function ($time) {
            $t = substr((string)$time, 0, 5); // HH:MM
            if (!str_contains($t, ':')) return 0;
            [$h, $m] = array_map('intval', explode(':', $t));
            return ($h * 60) + $m;
        };

        // ✅ OJO: en tu tabla es servicio_id (no id_servicio)
        $horarios = ServicioHorario::whereIn('servicio_id', $ids)
            ->where('dia_semana', $dow)
            ->get()
            ->groupBy('servicio_id');

        // Si algún servicio no tiene horarios ese día => no hay disponibilidad
        foreach ($ids as $idSrv) {
            if (!isset($horarios[$idSrv]) || $horarios[$idSrv]->isEmpty()) {
                return response()->json([]);
            }
        }

        // 1) Intervalos por servicio [startMin, endMin]
        $intervalsPerService = [];
        foreach ($ids as $idSrv) {
            $intervals = [];

            foreach ($horarios[$idSrv] as $h) {
                $sMin = $toMinutes($h->hora_inicio);
                $eMin = $toMinutes($h->hora_fin);

                // cruza medianoche (22:00 -> 02:00): para el día actual, hasta 24:00
                if ($eMin <= $sMin) {
                    $eMin = 24 * 60;
                }

                // seguridad
                $sMin = max(0, min($sMin, 24 * 60));
                $eMin = max(0, min($eMin, 24 * 60));

                if ($eMin > $sMin) $intervals[] = [$sMin, $eMin];
            }

            if (empty($intervals)) return response()->json([]);
            $intervalsPerService[] = $intervals;
        }

        // 2) Intersección entre servicios
        $common = array_shift($intervalsPerService);
        foreach ($intervalsPerService as $arr) {
            $newCommon = [];
            foreach ($common as [$a1, $a2]) {
                foreach ($arr as [$b1, $b2]) {
                    $s = max($a1, $b1);
                    $e = min($a2, $b2);
                    if ($e > $s) $newCommon[] = [$s, $e];
                }
            }
            $common = $newCommon;
            if (empty($common)) return response()->json([]);
        }

        // 3) Slots cada 30 min
        $step  = 30;
        $slots = [];

        foreach ($common as [$sMin, $eMin]) {
            for ($t = $sMin; $t + $totalMin <= $eMin; $t += $step) {
                $hh = intdiv($t, 60);
                $mm = $t % 60;

                $value = sprintf('%02d:%02d', $hh, $mm);
                $label = Carbon::createFromTime($hh, $mm)->format('g:i A');

                $slots[] = ['value' => $value, 'label' => $label];
            }
        }

        // 4) (Opcional) filtrar por empleados ocupados
        $empIds = collect($empleados)->map(fn($v) => (int)$v)->filter()->unique()->values();

        if ($empIds->isNotEmpty()) {
            $citas = Cita::whereDate('fecha_cita', $fecha->format('Y-m-d'))
                ->whereIn('id_empleado', $empIds)
                ->where('estado_cita', '!=', 'cancelada')
                ->get(['id_empleado', 'hora_cita', 'duracion_total_minutos']);

            $slots = array_values(array_filter($slots, function ($slot) use ($citas, $totalMin) {
                $start = Carbon::createFromFormat('H:i', $slot['value']);
                $end   = $start->copy()->addMinutes($totalMin);

                foreach ($citas as $c) {
                    // hora_cita normalmente viene "HH:MM:SS"
                    $cStart = Carbon::createFromFormat('H:i:s', $c->hora_cita);
                    $dur    = (int)($c->duracion_total_minutos ?? 60);
                    $cEnd   = $cStart->copy()->addMinutes(max(1, $dur));

                    if ($start < $cEnd && $end > $cStart) return false; // overlap
                }
                return true;
            }));
        }

        return response()->json($slots);
    }


    /**
     * Devuelve ventanas permitidas por servicio para esa fecha.
     * Si hay varios servicios => INTERSECCIÓN de ventanas.
     */
    private function buildAllowedWindowsForDateByServices(string $fecha, \Illuminate\Support\Collection $servicioIds): array
    {
        $date = \Carbon\Carbon::parse($fecha);
        $dow = (int)$date->dayOfWeekIso; // 1=Lunes...7=Domingo

        // Trae horarios de TODOS los servicios
        $horarios = \App\Models\ServicioHorario::query()
            ->whereIn('servicio_id', $servicioIds->all())
            ->where('dia_semana', $dow)
            ->get(['servicio_id','hora_inicio','hora_fin']);

        // Agrupar por servicio
        $byService = $horarios->groupBy('id_servicio');

        // Si algún servicio no tiene horario ese día => no hay horas
        foreach ($servicioIds as $sid) {
            if (empty($byService->get($sid)) || $byService->get($sid)->isEmpty()) {
                return [];
            }
        }

        // Convierte cada servicio a ventanas [start,end] (solo parte que cae en la fecha)
        $serviceWindows = [];
        foreach ($servicioIds as $sid) {
            $windows = [];
            foreach ($byService[$sid] as $h) {
                $start = \Carbon\Carbon::parse($fecha.' '.$h->hora_inicio);
                $end   = \Carbon\Carbon::parse($fecha.' '.$h->hora_fin);

                // Si cruza medianoche (ej 22:00 -> 12:30), end queda "antes"
                if ($end->lte($start)) {
                    $end->addDay(); // ventana real cruza al día siguiente
                }

                // Para el selector de ESTE día, nos quedamos con la parte que cae en [fecha 00:00, fecha 23:59:59]
                $dayStart = \Carbon\Carbon::parse($fecha.' 00:00:00');
                $dayEnd   = \Carbon\Carbon::parse($fecha.' 23:59:59');

                $cutStart = $start->copy()->max($dayStart);
                $cutEnd   = $end->copy()->min($dayEnd);

                if ($cutEnd->gt($cutStart)) {
                    $windows[] = [$cutStart, $cutEnd];
                }
            }
            $serviceWindows[] = $windows;
        }

        // INTERSECCIÓN de ventanas entre servicios
        $intersection = array_shift($serviceWindows);
        foreach ($serviceWindows as $wins) {
            $intersection = $this->intersectWindows($intersection, $wins);
            if (empty($intersection)) return [];
        }

        return $intersection;
    }

    /** Intersección de dos listas de ventanas */
    private function intersectWindows(array $a, array $b): array
    {
        $out = [];
        foreach ($a as [$aS, $aE]) {
            foreach ($b as [$bS, $bE]) {
                $s = $aS->copy()->max($bS);
                $e = $aE->copy()->min($bE);
                if ($e->gt($s)) $out[] = [$s, $e];
            }
        }
        return $out;
    }

    /**
     * Intervalos ocupados en esa fecha, usando duracion_total_minutos de cada cita.
     * (Excluye citaId si vienes de edit)
     */
    private function getBusyIntervalsForDate(string $fecha, $excludeCitaId = null): array
    {
        $q = \App\Models\Cita::query()
            ->whereDate('fecha_cita', $fecha)
            ->whereIn('estado_cita', ['pendiente','confirmada','completada']); // decide tu regla

        if ($excludeCitaId) {
            $q->where('id_cita', '!=', (int)$excludeCitaId);
        }

        $citas = $q->get(['id_cita','fecha_cita','hora_cita','duracion_total_minutos']);

        $busy = [];
        foreach ($citas as $c) {
            $start = \Carbon\Carbon::parse($c->fecha_cita.' '.$c->hora_cita);
            $mins  = max(1, (int)($c->duracion_total_minutos ?? 0));
            $end   = $start->copy()->addMinutes($mins);
            $busy[] = [$start, $end];
        }
        return $busy;
    }


    public function empleadosPorServicio(Request $request)
    {
        $servicioId = (int) $request->query('servicio_id', 0);
        if ($servicioId <= 0) return response()->json([]);

        $servicio = Servicio::query()->where('id_servicio', $servicioId)->first();
        if (!$servicio) return response()->json([]);

        $cat = trim((string) ($servicio->categoria ?? ''));
        $catLower = mb_strtolower($cat);

        // 1) Base: activos
        $base = Empleado::query();
        if (Schema::hasColumn('empleados', 'estatus')) {
            $base->whereRaw('LOWER(TRIM(estatus)) = ?', ['activo']);
        }

        // 2) Si hay categoría, intentamos filtrar por match
        $q = (clone $base);
        if ($cat !== '') {
            $q->where(function ($qq) use ($catLower) {
                if (Schema::hasColumn('empleados', 'departamento')) {
                    $qq->orWhereRaw('LOWER(TRIM(departamento)) = ?', [$catLower]);
                }
                if (Schema::hasColumn('empleados', 'puesto')) {
                    $qq->orWhereRaw('LOWER(TRIM(puesto)) = ?', [$catLower]);
                }
            });
        }

        $empleados = $q->orderBy('nombre')->get(['id', 'nombre', 'apellido']);

        // 3) Fallback: si no hubo match, devolvemos todos los activos
        if ($empleados->isEmpty()) {
            $empleados = $base->orderBy('nombre')->get(['id', 'nombre', 'apellido']);
        }

        return response()->json(
            $empleados->map(fn ($e) => [
                'id' => $e->id,
                'label' => trim($e->nombre.' '.$e->apellido),
            ])->values()
        );
    }

    public function index()
    {
        $citas = Cita::with(['cliente', 'servicio', 'empleado'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get();

        // Crear arreglo de eventos para el calendario
        $calendarEvents = $citas->map(function ($cita) {
            // formatear fecha y hora a ISO 8601
            $start = \Carbon\Carbon::parse($cita->fecha_cita->format('Y-m-d') . ' ' . $cita->hora_cita);

            return [
                'id'    => $cita->id_cita,
                'title' => $cita->servicio->nombre ?? 'Cita',
                'start' => $start->format('Y-m-d\TH:i:s'),
                // Opcional: si quieres duración fija de 1h
                'end'   => $start->copy()->addHour()->format('Y-m-d\TH:i:s'),
            ];
        })->values();

        $clientes = User::where('role_id', 1)->get();
        $servicios = Servicio::all();
        $empleados = User::where('role_id', 2)->get();
        $isGoogleConnected = GoogleToken::where('user_id', auth()->id())->exists();
        
        // Verificar conexión con Google Calendar de forma segura
        try {
            $isConnected = class_exists('App\Models\GoogleToken') && 
                        \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
        } catch (\Exception $e) {
            $isConnected = false;
        }

        return view('admin.citas.index', compact('citas', 'clientes', 'servicios', 'empleados', 'isConnected','isGoogleConnected', 'calendarEvents'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::select('id', 'nombre', 'email')->get();
        $servicios = Servicio::all();

        $empleados = User::query()
            ->where('users.role_id', 2)
            ->leftJoin('empleados', 'empleados.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('users.name')
            ->get();

        $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?: 'Sin departamento');

        $clientesForJs = $clientes->map(function ($c) {
            return [
                'id' => $c->id,
                'label' => trim(($c->nombre ?? '') . ' - ' . ($c->email ?? '')),
                'nombre' => $c->nombre ?? '',
                'email' => $c->email ?? '',
            ];
        })->values();

        $categorias = Servicio::query()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        // ✅ NUEVO: leer date del querystring y normalizarlo a Y-m-d
        $fechaPrefill = null;
        if ($request->filled('date')) {
            try {
                $fechaPrefill = Carbon::parse($request->query('date'))->format('Y-m-d');
            } catch (\Exception $e) {
                $fechaPrefill = null; // si viene algo raro, lo ignoramos
            }
        }

        $serviciosForJs = Servicio::query()
        ->selectRaw('
            id_servicio as id,
            nombre_servicio as nombre,
            categoria as categoria,
            precio as precio,
            duracion_minutos as duracion
        ')
        ->orderBy('categoria')
        ->orderBy('nombre_servicio')
        ->get();


        return view('admin.citas.create', compact(
            'empleadosPorDepto',
            'clientes',
            'servicios',
            'empleados',
            'clientesForJs',
            'categorias',
            'fechaPrefill',
            'serviciosForJs'
        ));
    }


    // En el método store, después de $cita = Cita::create($validated);
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente'   => ['required', 'exists:clientes,id'],
            'fecha_cita'   => ['required', 'date'],
            'hora_cita'    => ['required'],
            // ❌ ya no lo pedimos global, se decide desde servicios[]
            // 'id_empleado' => ['nullable', 'exists:empleados,id'],

            'estado_cita'  => ['required', Rule::in(['pendiente','confirmada','cancelada','completada'])],
            'observaciones'=> ['nullable', 'string'],
            'descuento'    => ['nullable', 'numeric', 'min:0'],
            'metodo_pago'  => ['nullable', Rule::in(['efectivo','transferencia','tarjeta'])],

            'servicios'                     => ['required', 'array', 'min:1'],
            'servicios.*.id_servicio'       => ['required', 'exists:servicios,id_servicio'],
            'servicios.*.id_empleado'       => ['nullable', 'exists:empleados,id'],   // ✅ NUEVO
            'servicios.*.precio_snapshot'   => ['nullable', 'numeric', 'min:0'],
            'servicios.*.duracion_snapshot' => ['nullable', 'integer', 'min:0'],
        ]);

        // Traer servicios para fallback (evitar confiar en front)
        $ids = collect($validated['servicios'])
            ->pluck('id_servicio')
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values();

        $serviciosDb = Servicio::whereIn('id_servicio', $ids)->get()->keyBy('id_servicio');

        $pivotData = [];
        $totalDuracion = 0;
        $totalMonto = 0;

        foreach ($validated['servicios'] as $item) {
            $id = (int) $item['id_servicio'];
            $srv = $serviciosDb->get($id);

            $precio = (isset($item['precio_snapshot']) && $item['precio_snapshot'] !== '')
                ? (float) $item['precio_snapshot']
                : (float) ($srv->precio ?? 0);

            $duracion = (isset($item['duracion_snapshot']) && $item['duracion_snapshot'] !== '')
                ? (int) $item['duracion_snapshot']
                : (int) ($srv->duracion_minutos ?? 0);

            $idEmpleadoServicio = !empty($item['id_empleado']) ? (int) $item['id_empleado'] : null;

            $pivotData[$id] = [
                'precio_snapshot'   => $precio,
                'duracion_snapshot' => $duracion,
                'id_empleado'       => $idEmpleadoServicio, // ✅ NUEVO (pivot)
            ];

            $totalMonto += $precio;
            $totalDuracion += $duracion;
        }

        // Para cumplir tu schema actual: id_servicio NO NULL
        $primaryServiceId = array_key_first($pivotData);

        // ✅ Empleado principal (para compatibilidad con citas/ventas/google)
        $primaryEmpleadoId = collect($validated['servicios'])
            ->pluck('id_empleado')
            ->filter()
            ->map(fn($v) => (int) $v)
            ->first();

        $cita = Cita::create([
            'id_cliente'             => $validated['id_cliente'],
            'id_servicio'            => $primaryServiceId,
            'id_empleado'            => $primaryEmpleadoId, // ✅ viene del primer row con empleado
            'fecha_cita'             => $validated['fecha_cita'],
            'hora_cita'              => $validated['hora_cita'],
            'duracion_total_minutos' => $totalDuracion,
            'descuento'              => $validated['descuento'] ?? 0,
            'estado_cita'            => $validated['estado_cita'],
            'metodo_pago'            => $validated['metodo_pago'] ?? null,
            'observaciones'          => $validated['observaciones'] ?? null,
            'synced_with_google'     => false,
        ]);

        // ✅ sync pivot con snapshots + id_empleado
        $cita->servicios()->sync($pivotData);

        // Enviar correo si está confirmada
        if ($cita->estado_cita === 'confirmada') {
            try {
                Mail::to($cita->cliente->email)->send(new CitaConfirmadaMail($cita));
            } catch (\Exception $e) {
                Log::error('Error enviando correo de confirmación: '.$e->getMessage());
            }
        }

        // Google Calendar
        $token = GoogleToken::first();
        if ($token) {
            try {
                $user = User::where('google_account_id', $token->google_account_id)->first();

                if ($user) {
                    $startTime = $cita->fecha_cita . 'T' . $cita->hora_cita;
                    $endTime = Carbon::parse($startTime)->addMinutes(max(1, (int)$totalDuracion))->toIso8601String();

                    $eventData = [
                        'summary' => 'Cita con ' . $cita->cliente->nombre,
                        'description' => $cita->observaciones ?? '',
                        'start' => ['dateTime' => $startTime, 'timeZone' => config('app.timezone')],
                        'end'   => ['dateTime' => $endTime,   'timeZone' => config('app.timezone')],
                    ];

                    $event = $this->googleCalendar->createEvent($user, $eventData);

                    $cita->update([
                        'google_event_id'    => $event->id,
                        'synced_with_google' => true,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sincronizando con Google Calendar: '.$e->getMessage());
            }
        }

        // Si entra como completada, crea la venta
        if ($cita->estado_cita === 'completada') {
            $this->crearVentaDesdeCita($cita);
        }

        return redirect()->route('admin.citas.index')->with('success', 'Cita creada correctamente.');
    }




    // En CitaController.php - método show
    public function show(Cita $cita)
    {
        // Cargar todas las relaciones necesarias
        $cita->load(['cliente', 'servicio', 'empleado', 'venta']);
        
        return view('admin.citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        $cita->load('servicios'); // 👈 importante para multi-servicio

        // ✅ Empleados = users(role_id=2) + join a empleados para traer "departamento"
        $empleados = User::query()
            ->where('users.role_id', 2)
            ->leftJoin('empleados', 'empleados.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('users.name')
            ->get();

            $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?: 'Sin departamento');

            $clientes = Cliente::select('id', 'nombre', 'email')->get();
            $servicios = Servicio::all();
            $categorias = Servicio::query()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

            $serviciosForJs = Servicio::query()
                ->selectRaw('
                    id_servicio as id,
                    nombre_servicio as nombre,
                    categoria as categoria,
                    precio as precio,
                    duracion_minutos as duracion
                ')
                ->orderBy('categoria')
                ->orderBy('nombre_servicio')
                ->get();


          // ✅ Servicios seleccionados para precargar en edit
            $serviciosSeleccionados = $cita->servicios->map(function ($s) {
                return [
                    'id_servicio'        => (int) $s->id_servicio,
                    'nombre'             => $s->nombre_servicio,
                    'categoria'           => $s->categoria ?? '',
                    'precio_snapshot'    => (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0),
                    'duracion_snapshot'  => (int) ($s->pivot->duracion_snapshot ?? $s->duracion_minutos ?? 0),
                ];
            })->values();

            // ✅ Fallback si es una cita vieja sin pivot
            if ($serviciosSeleccionados->isEmpty() && !empty($cita->id_servicio)) {
                $s = Servicio::where('id_servicio', $cita->id_servicio)->first();
                if ($s) {
                    $serviciosSeleccionados = collect([[
                        'id_servicio'       => (int) $s->id_servicio,
                        'nombre'            => $s->nombre_servicio,
                        'categoria'          => $s->categoria ?? '',
                        'precio_snapshot'   => (float) ($s->precio ?? 0),
                        'duracion_snapshot' => (int) ($s->duracion_minutos ?? 0),
                    ]]);
                }
            }

            $totalServicios = (float) $serviciosSeleccionados->sum('precio_snapshot');
            $duracionTotal  = (int) $serviciosSeleccionados->sum('duracion_snapshot');

            return view('admin.citas.edit', compact(
                'empleadosPorDepto',
                'cita',
                'clientes',
                'servicios',
                'empleados',
                'categorias',
                'serviciosForJs',
                'serviciosSeleccionados',
                'totalServicios',
                'duracionTotal'
            ));
        }
    


    public function update(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        $oldEstado = $cita->estado_cita;

        $validated = $request->validate([
            'id_cliente'   => ['required', 'exists:clientes,id'],
            'fecha_cita'   => ['required', 'date'],
            'hora_cita'    => ['required'],
            // ❌ ya no lo pedimos global
            // 'id_empleado' => ['nullable', 'exists:empleados,id'],

            'estado_cita'  => ['required', Rule::in(['pendiente','confirmada','cancelada','completada'])],
            'observaciones'=> ['nullable', 'string'],
            'descuento'    => ['nullable', 'numeric', 'min:0'],
            'metodo_pago'  => ['nullable', Rule::in(['efectivo','transferencia','tarjeta'])],

            'servicios'                     => ['required', 'array', 'min:1'],
            'servicios.*.id_servicio'       => ['required', 'exists:servicios,id_servicio'],
            'servicios.*.id_empleado'       => ['nullable', 'exists:empleados,id'],  // ✅ NUEVO
            'servicios.*.precio_snapshot'   => ['nullable', 'numeric', 'min:0'],
            'servicios.*.duracion_snapshot' => ['nullable', 'integer', 'min:0'],
        ]);

        $ids = collect($validated['servicios'])
            ->pluck('id_servicio')
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values();

        $serviciosDb = Servicio::whereIn('id_servicio', $ids)->get()->keyBy('id_servicio');

        $pivotData = [];
        $totalDuracion = 0;
        $totalMonto = 0;

        foreach ($validated['servicios'] as $item) {
            $idSrv = (int) $item['id_servicio'];
            $srv = $serviciosDb->get($idSrv);

            $precio = (isset($item['precio_snapshot']) && $item['precio_snapshot'] !== '')
                ? (float) $item['precio_snapshot']
                : (float) ($srv->precio ?? 0);

            $duracion = (isset($item['duracion_snapshot']) && $item['duracion_snapshot'] !== '')
                ? (int) $item['duracion_snapshot']
                : (int) ($srv->duracion_minutos ?? 0);

            $idEmpleadoServicio = !empty($item['id_empleado']) ? (int) $item['id_empleado'] : null;

            $pivotData[$idSrv] = [
                'precio_snapshot'   => $precio,
                'duracion_snapshot' => $duracion,
                'id_empleado'       => $idEmpleadoServicio, // ✅ NUEVO (pivot)
            ];

            $totalMonto += $precio;
            $totalDuracion += $duracion;
        }

        $primaryServiceId = array_key_first($pivotData);

        // ✅ Empleado principal (para compatibilidad con citas/ventas/google)
        $primaryEmpleadoId = collect($validated['servicios'])
            ->pluck('id_empleado')
            ->filter()
            ->map(fn($v) => (int) $v)
            ->first();

        $cita->update([
            'id_cliente'             => $validated['id_cliente'],
            'id_servicio'            => $primaryServiceId,
            'id_empleado'            => $primaryEmpleadoId,
            'fecha_cita'             => $validated['fecha_cita'],
            'hora_cita'              => $validated['hora_cita'],
            'duracion_total_minutos' => $totalDuracion,
            'descuento'              => $validated['descuento'] ?? 0,
            'estado_cita'            => $validated['estado_cita'],
            'metodo_pago'            => $validated['metodo_pago'] ?? null,
            'observaciones'          => $validated['observaciones'] ?? null,
        ]);

        // sync pivot
        $cita->servicios()->sync($pivotData);

        // Google Calendar update (usa duración total)
        $token = GoogleToken::first();
        if ($token && $cita->google_event_id) {
            try {
                $user = User::where('google_account_id', $token->google_account_id)->first();
                if ($user) {
                    $startTime = $cita->fecha_cita . 'T' . $cita->hora_cita;
                    $endTime = Carbon::parse($startTime)->addMinutes(max(1, (int)$totalDuracion))->toIso8601String();

                    $eventData = [
                        'summary' => 'Cita con ' . $cita->cliente->nombre,
                        'description' => $cita->observaciones ?? '',
                        'start' => ['dateTime' => $startTime, 'timeZone' => config('app.timezone')],
                        'end'   => ['dateTime' => $endTime,   'timeZone' => config('app.timezone')],
                    ];

                    $this->googleCalendar->updateEvent($user, $cita->google_event_id, $eventData);
                }
            } catch (\Exception $e) {
                Log::error('Error actualizando evento Google: '.$e->getMessage());
            }
        }

        // Si pasa a completada, crea la venta (si no existía)
        if ($oldEstado !== 'completada' && $cita->estado_cita === 'completada') {
            $this->crearVentaDesdeCita($cita);
        }

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada correctamente.');
    }



    //=======================================================================
    //CREAR VENTA DESDE CITA FUNCIÓN ========================================
    private function crearVentaDesdeCita(Cita $cita): void
    {
        // Cargar relaciones necesarias (multi-servicio o servicio principal)
        $cita->loadMissing(['servicios', 'servicio']);

        // Si falta empleado o cliente, no podemos crear venta (ventas los requiere)
        if (empty($cita->id_cliente) || empty($cita->id_empleado)) {
            \Log::warning('Cita completada sin cliente/empleado, no se creó venta', [
                'id_cita' => $cita->id_cita,
                'id_cliente' => $cita->id_cliente,
                'id_empleado' => $cita->id_empleado,
            ]);
            return;
        }

        // 1) Subtotal: suma snapshots del pivot (si existen) o fallback al servicio principal
        $subtotal = $cita->servicios->isNotEmpty()
            ? (float) $cita->servicios->sum(fn ($s) => (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0))
            : (float) ($cita->servicio->precio ?? 0);

        // 2) Descuento y total
        $descuento = (float) ($cita->descuento ?? 0);
        $total = max($subtotal - $descuento, 0);

        // 3) Fecha venta = fecha_cita + hora_cita
        $fecha = $cita->fecha_cita instanceof \Carbon\Carbon
            ? $cita->fecha_cita->format('Y-m-d')
            : (string) $cita->fecha_cita;

        $fechaVenta = Carbon::parse($fecha . ' ' . $cita->hora_cita);

        // 4) Método de pago de CITA -> ENUM de VENTAS
        // En citas: efectivo | transferencia | tarjeta
        // En ventas: efectivo | tarjeta_credito | tarjeta_debito
        $metodo = strtolower((string) ($cita->metodo_pago ?? ''));

        $formaPago = match ($metodo) {
            'efectivo' => 'efectivo',
            'tarjeta' => 'tarjeta_credito',         // si no distingues, mándalo a crédito
            'tarjeta_credito' => 'tarjeta_credito',
            'tarjeta_debito' => 'tarjeta_debito',
            'transferencia' => 'efectivo',          // ventas no acepta transferencia (lo reportamos desde citas)
            default => 'efectivo',
        };

        // 5) Crear o actualizar venta (evita duplicados por id_cita)
        Venta::updateOrCreate(
            ['id_cita' => $cita->id_cita],
            [
                'id_cliente' => $cita->id_cliente,      // ✅ OJO: debe apuntar a clientes.id (ver migración abajo)
                'id_empleado' => $cita->id_empleado,    // ✅ users.id (role=2)
                'id_servicio' => $cita->id_servicio,
                'fecha_venta' => $fechaVenta,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'forma_pago' => $formaPago,
                'estado_venta' => 'pagada',             // ✅ válido: pendiente|pagada|cancelada
                'observaciones' => $cita->observaciones,
            ]
        );
    }

    public function destroy(Cita $cita)
    {
        try {
            // Eliminar de Google Calendar si existe
            try {
                $tokenExists = \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
                if ($tokenExists && $cita->google_event_id) {
                    $this->googleCalendar->deleteEventFromCita($cita);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting cita from Google Calendar: ' . $e->getMessage());
            }

            $cita->delete();

            return redirect()->route('admin.citas.index')
                ->with('success', 'Cita eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la cita: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar cita específica con Google Calendar
     */
// Agrega estos métodos al CitaController
    public function syncWithGoogle(Cita $cita)
    {
        try {
            $event = $this->googleCalendar->createEventFromCita($cita);
            
            $cita->update([
                'google_event_id' => $event->getId(),
                'synced_with_google' => true,
                'last_sync_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Cita sincronizada con Google Calendar exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al sincronizar con Google Calendar: ' . $e->getMessage());
        }
    }

    public function syncAllWithGoogle()
    {
        try {
            $results = $this->googleCalendar->syncPendingCitas();

            $message = "Sincronización completada: {$results['success']} citas sincronizadas.";
            if (!empty($results['errors'])) {
                $message .= " Errores: " . implode(', ', $results['errors']);
            }

            return redirect()->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en la sincronización: ' . $e->getMessage());
        }
    }
}