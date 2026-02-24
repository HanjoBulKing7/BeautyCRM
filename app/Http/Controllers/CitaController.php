<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Models\Servicio;
use App\Models\CategoriaServicio;
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
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    // ============================================================
    // ✅ Helpers para disponibilidad (reutilizables)
    // ============================================================

    private function extractDateServiciosEmpleados(Request $request): array
    {
        // ✅ soporta fecha/date
        $date = $request->query('fecha') ?? $request->query('date') ?? $request->input('fecha_cita') ?? $request->input('date');

        $items = $request->query('items');
        if (!is_array($items)) $items = $request->input('items');

        // ✅ si viene items[0][id_servicio]...
        if (is_array($items) && count($items) > 0) {
            $servicios = collect($items)->pluck('id_servicio')->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
            $empleados = collect($items)->pluck('id_empleado')->map(fn($v) => (int)$v)->filter()->unique()->values()->all();

            return [$date, $servicios, $empleados];
        }

        // ✅ fallback legacy: servicios[]=, empleados[]=
        $servicios = $request->query('servicios', $request->input('servicios', []));
        $empleados = $request->query('empleados', $request->input('empleados', []));

        $servicios = collect(is_array($servicios) ? $servicios : [])->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        $empleados = collect(is_array($empleados) ? $empleados : [])->map(fn($v) => (int)$v)->filter()->unique()->values()->all();

        return [$date, $servicios, $empleados];
    }

    private function computeSlotsForDate(string $date, array $servicios, array $empleados = []): array
    {
        if (!$date || !is_array($servicios) || count($servicios) === 0) {
            return [];
        }

        $fecha = Carbon::parse($date);
        $dow   = $fecha->dayOfWeekIso; // 1..7 (Lun..Dom)

        $ids = collect($servicios)
            ->map(fn($v) => (int)$v)
            ->filter()
            ->unique()
            ->values();

        // 🔥 servicios usan PK id_servicio
        $serviciosDb = Servicio::whereIn('id_servicio', $ids)->get()->keyBy('id_servicio');
        if ($serviciosDb->isEmpty()) return [];

        // Duración total
        $totalMin = (int) $ids->sum(fn($id) => (int)($serviciosDb[$id]->duracion_minutos ?? 0));
        if ($totalMin <= 0) $totalMin = 30;

        // helper robusto: "HH:MM" o "HH:MM:SS" -> minutos del día
        $toMinutes = function ($time) {
            $t = substr((string)$time, 0, 5); // HH:MM
            if (!str_contains($t, ':')) return 0;
            [$h, $m] = array_map('intval', explode(':', $t));
            return ($h * 60) + $m;
        };

        // ✅ en tu tabla es servicio_id
        $horarios = ServicioHorario::whereIn('servicio_id', $ids)
            ->where('dia_semana', $dow)
            ->get()
            ->groupBy('servicio_id');

        // Si algún servicio no tiene horarios ese día => no hay disponibilidad
        foreach ($ids as $idSrv) {
            if (!isset($horarios[$idSrv]) || $horarios[$idSrv]->isEmpty()) {
                return [];
            }
        }

        // 1) Intervalos por servicio [startMin, endMin]
        $intervalsPerService = [];
        foreach ($ids as $idSrv) {
            $intervals = [];

            foreach ($horarios[$idSrv] as $h) {
                $sMin = $toMinutes($h->hora_inicio);
                $eMin = $toMinutes($h->hora_fin);

                // cruza medianoche: para el día actual, hasta 24:00
                if ($eMin <= $sMin) $eMin = 24 * 60;

                $sMin = max(0, min($sMin, 24 * 60));
                $eMin = max(0, min($eMin, 24 * 60));

                if ($eMin > $sMin) $intervals[] = [$sMin, $eMin];
            }

            if (empty($intervals)) return [];
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
            if (empty($common)) return [];
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
                ->whereIn('empleado_id', $empIds)
                ->where('estado_cita', '!=', 'cancelada')
                ->get(['empleado_id', 'hora_cita', 'duracion_total_minutos']);

            $slots = array_values(array_filter($slots, function ($slot) use ($citas, $totalMin) {
                $start = Carbon::createFromFormat('H:i', $slot['value']);
                $end   = $start->copy()->addMinutes($totalMin);

                foreach ($citas as $c) {
                    // robusto: soporta H:i:s o H:i
                    $raw = (string)($c->hora_cita ?? '');
                    $raw8 = substr($raw, 0, 8);
                    $raw5 = substr($raw, 0, 5);

                    try {
                        $cStart = Carbon::createFromFormat('H:i:s', $raw8);
                    } catch (\Exception $e) {
                        $cStart = Carbon::createFromFormat('H:i', $raw5);
                    }

                    $dur    = (int)($c->duracion_total_minutos ?? 60);
                    $cEnd   = $cStart->copy()->addMinutes(max(1, $dur));

                    if ($start < $cEnd && $end > $cStart) return false; // overlap
                }
                return true;
            }));
        }

        return $slots;
    }

    // ============================================================
    // ✅ AJAX: horas disponibles (para tu grid JS)
    // ============================================================

    public function horasDisponibles(Request $request)
    {
        [$date, $servicios, $empleados] = $this->extractDateServiciosEmpleados($request);

        if (!$date || empty($servicios)) {
            return response()->json([
                'horas' => [],
                'slots' => [],
            ]);
        }

        $slots = $this->computeSlotsForDate((string)$date, (array)$servicios, (array)$empleados);

        // ✅ para tu JS nuevo (grid): array de strings "HH:MM"
        $horas = array_values(array_map(fn($s) => $s['value'], $slots));

        // ✅ compat: dejo slots también
        return response()->json([
            'horas' => $horas,
            'slots' => $slots,
        ]);
    }

    // ============================================================
    // ✅ AJAX: disponibilidad por mes (dots/disabled por día lleno)
    // ============================================================

    public function availabilityMonth(Request $request)
    {
        $month = (string) $request->query('month', '');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json([
                'ok' => false,
                'message' => 'month debe ser YYYY-MM',
            ], 422);
        }

        // items[] preferido; fallback a servicios[]/empleados[]
        [$ignoreDate, $servicios, $empleados] = $this->extractDateServiciosEmpleados($request);

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $today = Carbon::today();

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $ymd = $d->format('Y-m-d');

            if ($d->lt($today)) {
                $days[$ymd] = ['disabled' => true, 'slots' => 0];
                continue;
            }

            if (empty($servicios) || empty($empleados)) {
                // si aún no hay empleados/servicios, lo marcamos "no disponible"
                $days[$ymd] = ['disabled' => true, 'slots' => 0];
                continue;
            }

            $slots = $this->computeSlotsForDate($ymd, $servicios, $empleados);
            $days[$ymd] = ['disabled' => count($slots) === 0, 'slots' => count($slots)];
        }

        return response()->json([
            'ok' => true,
            'month' => $month,
            'days' => $days,
        ]);
    }

    // ============================================================
    // (lo demás lo dejo tal como lo tenías, solo toques mínimos)
    // ============================================================

    /**
     * Devuelve ventanas permitidas por servicio para esa fecha.
     * Si hay varios servicios => INTERSECCIÓN de ventanas.
     */
    private function buildAllowedWindowsForDateByServices(string $fecha, \Illuminate\Support\Collection $servicioIds): array
    {
        $date = \Carbon\Carbon::parse($fecha);
        $dow = (int)$date->dayOfWeekIso; // 1=Lunes...7=Domingo

        $horarios = \App\Models\ServicioHorario::query()
            ->whereIn('servicio_id', $servicioIds->all())
            ->where('dia_semana', $dow)
            ->get(['servicio_id','hora_inicio','hora_fin']);

        $byService = $horarios->groupBy('servicio_id');

        foreach ($servicioIds as $sid) {
            if (empty($byService->get($sid)) || $byService->get($sid)->isEmpty()) {
                return [];
            }
        }

        $serviceWindows = [];
        foreach ($servicioIds as $sid) {
            $windows = [];
            foreach ($byService[$sid] as $h) {
                $start = \Carbon\Carbon::parse($fecha.' '.$h->hora_inicio);
                $end   = \Carbon\Carbon::parse($fecha.' '.$h->hora_fin);

                if ($end->lte($start)) {
                    $end->addDay();
                }

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

        $intersection = array_shift($serviceWindows);
        foreach ($serviceWindows as $wins) {
            $intersection = $this->intersectWindows($intersection, $wins);
            if (empty($intersection)) return [];
        }

        return $intersection;
    }

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

    private function getBusyIntervalsForDate(string $fecha, $excludeCitaId = null): array
    {
        $q = \App\Models\Cita::query()
            ->whereDate('fecha_cita', $fecha)
            ->whereIn('estado_cita', ['pendiente','confirmada','completada']);

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

    /**
     * ✅ Regla “responsable”:
     * - Más servicios (count)
     * - Empate -> mayor duración total (total)
     * - Empate -> mayor servicio individual (maxSingle)
     */
    private function resolverEmpleadoResponsable(array $serviciosInput, $serviciosDb): ?int
    {
        $stats = []; // [empId => ['count'=>, 'total'=>, 'maxSingle'=>]]

        foreach ($serviciosInput as $item) {
            $emp = !empty($item['id_empleado']) ? (int) $item['id_empleado'] : null;
            if (!$emp) continue;

            $idSrv = (int) ($item['id_servicio'] ?? 0);
            $srv   = $serviciosDb->get($idSrv);

            $dur = (isset($item['duracion_snapshot']) && $item['duracion_snapshot'] !== '')
                ? (int) $item['duracion_snapshot']
                : (int) ($srv->duracion_minutos ?? 0);

            if (!isset($stats[$emp])) {
                $stats[$emp] = ['count' => 0, 'total' => 0, 'maxSingle' => 0];
            }

            $stats[$emp]['count']++;
            $stats[$emp]['total'] += max(0, $dur);
            $stats[$emp]['maxSingle'] = max($stats[$emp]['maxSingle'], max(0, $dur));
        }

        if (empty($stats)) return null;

        uksort($stats, function ($a, $b) use ($stats) {
            $A = $stats[$a]; $B = $stats[$b];

            if ($A['count'] !== $B['count']) return $B['count'] <=> $A['count'];
            if ($A['total'] !== $B['total']) return $B['total'] <=> $A['total'];
            if ($A['maxSingle'] !== $B['maxSingle']) return $B['maxSingle'] <=> $A['maxSingle'];
            return $a <=> $b;
        });

        return (int) array_key_first($stats);
    }

    public function empleadosPorServicio(Request $request)
    {
        $servicioId = (int) $request->query('servicio_id', 0);
        if ($servicioId <= 0) return response()->json([]);

        $servicio = Servicio::with('categoria')->where('id_servicio', $servicioId)->first();
        if (!$servicio) return response()->json([]);

        $cat = trim((string) ($servicio->categoria->nombre ?? ''));
        $catLower = mb_strtolower($cat);

        $base = Empleado::query()
            ->join('users', 'users.id', '=', 'empleados.user_id')
            ->where('users.role_id', 2);

        if (Schema::hasColumn('empleados', 'estatus')) {
            $base->whereRaw('LOWER(TRIM(estatus)) = ?', ['activo']);
        }

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

        $empleados = $q->orderBy('empleados.nombre')->get(['empleados.id', 'empleados.nombre', 'empleados.apellido']);

        if ($empleados->isEmpty()) {
            $empleados = $base->orderBy('empleados.nombre')
                ->get(['empleados.id', 'empleados.nombre', 'empleados.apellido']);
        }

        return response()->json(
            $empleados->map(fn ($e) => [
                'id'      => (int)$e->id,
                'nombre'  => (string)($e->nombre ?? ''),
                'apellido'=> (string)($e->apellido ?? ''),
                'label'   => trim(($e->nombre ?? '').' '.($e->apellido ?? '')),
            ])->values()
        );
    }

    public function index()
    {
        $citas = Cita::with(['cliente', 'empleado', 'servicios'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get();

        $calendarEvents = $citas->map(function ($cita) {
            $start = \Carbon\Carbon::parse($cita->fecha_cita->format('Y-m-d') . ' ' . $cita->hora_cita);
            $firstServicio = $cita->servicios->first();

            return [
                'id'    => $cita->id_cita,
                'title' => $firstServicio->nombre_servicio ?? 'Cita',
                'start' => $start->format('Y-m-d\TH:i:s'),
                'end'   => $start->copy()->addHour()->format('Y-m-d\TH:i:s'),
            ];
        })->values();

        $clientes = Cliente::latest()->get();
        $servicios = Servicio::all();
        $empleados = Empleado::latest()->get();

        $isGoogleConnected = GoogleToken::where('user_id', auth()->id())->exists();

        try {
            $isConnected = class_exists('App\Models\GoogleToken') &&
                        \App\Models\GoogleToken::where('user_id', auth()->id())->exists();
        } catch (\Exception $e) {
            $isConnected = false;
        }

        return view('admin.citas.index', compact(
            'citas',
            'clientes',
            'servicios',
            'empleados',
            'isConnected',
            'isGoogleConnected',
            'calendarEvents'
        ));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::select('id', 'nombre', 'email')->get();

        $servicios = Servicio::with('categoria')->get();

        $empleados = Empleado::query()
            ->leftJoin('users', 'users.id', '=', 'empleados.user_id')
            ->where('users.role_id', 2)
            ->select([
                'empleados.id as id',
                'empleados.nombre',
                'empleados.apellido',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('empleados.nombre')
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

        $serviciosForJs = $servicios->map(function ($s) {
            $catName = $s->categoria->nombre ?? 'Sin categoría';

            return [
                'id'       => (int) $s->id_servicio,
                'nombre'   => (string) $s->nombre_servicio,
                'categoria'=> (string) $catName,
                'precio'   => (float) ($s->precio ?? 0),
                'duracion' => (int) ($s->duracion_minutos ?? 0),
            ];
        })->values();

        $categorias = $serviciosForJs->pluck('categoria')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $fechaPrefill = null;
        if ($request->filled('date')) {
            try {
                $fechaPrefill = Carbon::parse($request->query('date'))->format('Y-m-d');
            } catch (\Exception $e) {
                $fechaPrefill = null;
            }
        }

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

    public function store(Request $request)
    {
        // ✅ compat: si tu UI nueva manda items[], lo tratamos como servicios[]
        if (!$request->has('servicios') && $request->has('items')) {
            $request->merge(['servicios' => $request->input('items')]);
        }

        $validated = $request->validate([
            'cliente_id'   => ['required', 'exists:clientes,id'],
            'fecha_cita'   => ['required', 'date'],
            'hora_cita'    => ['required'],

            'estado_cita'  => ['required', Rule::in(['pendiente','confirmada','cancelada','completada'])],
            'metodo_pago'  => ['nullable', Rule::in(['efectivo','tarjeta_credito','tarjeta_debito','transferencia']), 'required_if:estado_cita,completada'],
            'observaciones'=> ['nullable', 'string'],
            'descuento'    => ['nullable', 'numeric', 'min:0'],

        'servicios'                     => ['required', 'array', 'min:1'],
        'servicios.*.id_servicio'       => ['required', 'exists:servicios,id_servicio'],
        'servicios.*.id_empleado'       => ['nullable', 'exists:empleados,id'],
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
            'id_empleado'       => $idEmpleadoServicio,
        ];

        $totalMonto += $precio;
        $totalDuracion += $duracion;
    }

        $responsableId = $this->resolverEmpleadoResponsable($validated['servicios'], $serviciosDb);
        $metodoPago = $validated['metodo_pago'] ?? null;

    $cita = Cita::create([
        'cliente_id'            => $validated['cliente_id'],
        'empleado_id'           => $responsableId,
        'fecha_cita'            => $validated['fecha_cita'],
        'hora_cita'             => $validated['hora_cita'],
        'duracion_total_minutos'=> $totalDuracion,
        'descuento'             => $validated['descuento'] ?? 0,
        'estado_cita'           => $validated['estado_cita'],
        'observaciones'         => $validated['observaciones'] ?? null,
        'synced_with_google'    => false,
    ]);

    $cita->servicios()->sync($pivotData);

        if ($cita->estado_cita === 'confirmada') {
            try {
                Mail::to($cita->cliente->email)->send(new CitaConfirmadaMail($cita));
            } catch (\Exception $e) {
                Log::error('Error enviando correo de confirmación: '.$e->getMessage());
            }
        }

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

        if ($cita->estado_cita === 'completada') {
            $this->crearVentaDesdeCita($cita, (string)$metodoPago);
        }

        return redirect()->route('admin.citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function show(Cita $cita)
    {
        $cita->load(['cliente', 'empleado', 'servicios', 'venta']);
        return view('admin.citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        $cita->load(['servicios.categoria']);

        $empleados = Empleado::query()
            ->leftJoin('users', 'users.id', '=', 'empleados.user_id')
            ->where('users.role_id', 2)
            ->select([
                'empleados.id as id',
                'empleados.nombre',
                'empleados.apellido',
                'users.email',
                'empleados.departamento',
                'empleados.puesto',
            ])
            ->orderByRaw('COALESCE(empleados.departamento, "") ASC')
            ->orderBy('empleados.nombre')
            ->get();

        $empleadosPorDepto = $empleados->groupBy(fn($e) => $e->departamento ?: 'Sin departamento');

        $clientes = Cliente::select('id', 'nombre', 'email')->get();
        $servicios = Servicio::with('categoria')->get();

        $serviciosForJs = $servicios->map(function ($s) {
            $catName = $s->categoria->nombre ?? 'Sin categoría';

            return [
                'id'       => (int) $s->id_servicio,
                'nombre'   => (string) $s->nombre_servicio,
                'categoria'=> (string) $catName,
                'precio'   => (float) ($s->precio ?? 0),
                'duracion' => (int) ($s->duracion_minutos ?? 0),
            ];
        })->values();

        $categorias = $serviciosForJs->pluck('categoria')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $serviciosSeleccionados = $cita->servicios->map(function ($s) {
            $catName = $s->categoria->nombre ?? 'Sin categoría';

            return [
                'id_servicio'        => (int) $s->id_servicio,
                'nombre'             => $s->nombre_servicio,
                'categoria'          => (string) $catName,
                'precio_snapshot'    => (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0),
                'duracion_snapshot'  => (int) ($s->pivot->duracion_snapshot ?? $s->duracion_minutos ?? 0),
                'id_empleado'        => $s->pivot->id_empleado ?? null,
            ];
        })->values();

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
        // ✅ compat: si tu UI nueva manda items[], lo tratamos como servicios[]
        if (!$request->has('servicios') && $request->has('items')) {
            $request->merge(['servicios' => $request->input('items')]);
        }

        $cita = Cita::findOrFail($id);
        $oldEstado = $cita->estado_cita;

        $validated = $request->validate([
            'cliente_id'   => ['required', 'exists:clientes,id'],
            'fecha_cita'   => ['required', 'date'],
            'hora_cita'    => ['required'],

            'estado_cita'  => ['required', Rule::in(['pendiente','confirmada','cancelada','completada'])],
            'metodo_pago'  => ['nullable', Rule::in(['efectivo','tarjeta_credito','tarjeta_debito','transferencia']), 'required_if:estado_cita,completada'],
            'observaciones'=> ['nullable', 'string'],
            'descuento'    => ['nullable', 'numeric', 'min:0'],

            'servicios'                     => ['required', 'array', 'min:1'],
            'servicios.*.id_servicio'       => ['required', 'exists:servicios,id_servicio'],
            'servicios.*.id_empleado'       => ['nullable', 'exists:empleados,id'],
            'servicios.*.precio_snapshot'   => ['nullable', 'numeric', 'min:0'],
            'servicios.*.duracion_snapshot' => ['nullable', 'integer', 'min:0'],
        ]);

        $metodoPago = $validated['metodo_pago'] ?? null;

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
            'id_empleado'       => $idEmpleadoServicio,
        ];

        $totalMonto += $precio;
        $totalDuracion += $duracion;
    }

        $responsableId = $this->resolverEmpleadoResponsable($validated['servicios'], $serviciosDb);

    $cita->update([
        'cliente_id'            => $validated['cliente_id'],
        'empleado_id'           => $responsableId,
        'fecha_cita'            => $validated['fecha_cita'],
        'hora_cita'             => $validated['hora_cita'],
        'duracion_total_minutos'=> $totalDuracion,
        'descuento'             => $validated['descuento'] ?? 0,
        'estado_cita'           => $validated['estado_cita'],
        'observaciones'         => $validated['observaciones'] ?? null,
    ]);

    $cita->servicios()->sync($pivotData);

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

        if ($cita->estado_cita === 'completada') {
            $this->crearVentaDesdeCita($cita, (string)$metodoPago);
        }

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    //=======================================================================
    // CREAR VENTA DESDE CITA
    private function crearVentaDesdeCita(Cita $cita, string $formaPago): Venta
    {
        return DB::transaction(function () use ($cita, $formaPago) {

            $cita->loadMissing(['servicios']);

            $subtotal = (float) $cita->servicios->sum(function ($s) {
                return (float) ($s->pivot->precio_snapshot ?? $s->precio ?? 0);
            });

            $descuento = (float) ($cita->descuento ?? 0);
            $total = max($subtotal - $descuento, 0);

            $fechaVenta = Carbon::parse($cita->fecha_cita)->setTimeFromTimeString($cita->hora_cita);

            return Venta::updateOrCreate(
                ['id_cita' => $cita->id_cita],
                [
                    'fecha_venta'            => $fechaVenta,
                    'total'                  => $total,
                    'forma_pago'             => $formaPago,
                    'estado_venta'           => 'pagada',
                    'metodo_pago_especifico' => null,
                    'referencia_pago'        => null,
                    'notas'                  => $cita->observaciones,
                    'comision_empleado'      => 0,
                ]
            );
        });
    }

    public function destroy(Cita $cita)
    {
        try {
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
