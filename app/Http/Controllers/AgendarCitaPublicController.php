<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AgendarCitaPublicController extends Controller
{
    private const TOLERANCIA_MINUTOS = 10;
    private const SLOT_STEP_MINUTOS = 15;

    public function create(Request $request)
    {
        // ✅ Ya estás en middleware(auth), no necesitamos redirects

        $servicios = Servicio::query()
            ->where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        // ✅ Soporta ?servicio=ID y ?servicio_id=ID
        $servicioId = (int) ($request->query('servicio') ?? $request->query('servicio_id') ?? 0);

        $servicioSeleccionado = $servicioId
            ? $servicios->firstWhere('id_servicio', $servicioId)
            : null;

        // ✅ Categorías (ajusta columnas si tu tabla usa otros nombres)
        $categorias = DB::table('categorias_servicios')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['id_categoria', 'nombre']);

        // ✅ Mapa servicios para JS (incluye id_categoria)
        $serviciosJs = $servicios->mapWithKeys(function ($s) {
            return [
                $s->id_servicio => [
                    'id_servicio'       => $s->id_servicio,
                    'id_categoria'      => $s->id_categoria ?? null,
                    'nombre_servicio'   => $s->nombre_servicio,
                    'precio'            => (float) $s->precio,
                    'descuento'         => (float) ($s->descuento ?? 0),
                    'duracion_minutos'  => (int) ($s->duracion_minutos ?? 0),
                    'imagen'            => $s->imagen ?? null,
                    'caracteristicas'   => $s->caracteristicas ?? null,
                ]
            ];
        });

        $empleados = DB::table('empleados')
            ->where('estatus', 'activo')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellido']);

        return view('agendarcita', [
            'servicios'            => $servicios,
            'servicioSeleccionado' => $servicioSeleccionado,
            'serviciosJs'          => $serviciosJs,
            'categorias'           => $categorias,
            'empleados'            => $empleados,
        ]);
    }

    public function store(Request $request)
    {
        // ✅ Resolver cliente_id desde clientes.user_id = Auth::id()
        $clienteId = (int) DB::table('clientes')->where('user_id', Auth::id())->value('id');
        if (!$clienteId) {
            return back()->with('error', 'No se encontró el perfil de cliente asociado a tu cuenta.')->withInput();
        }

        $request->validate([
            'fecha_cita'    => ['required', 'date'],
            'hora_cita'     => ['required', 'date_format:H:i'],
            'observaciones' => ['nullable', 'string', 'max:2000'],

            'items'                     => ['required', 'array', 'min:1'],
            'items.*.id_servicio'       => ['required', 'integer', 'exists:servicios,id_servicio'],
            'items.*.id_empleado'       => ['required', 'integer', 'exists:empleados,id'],
            'items.*.orden'             => ['required', 'integer', 'min:1'],
        ]);

        // ✅ No días pasados
        $fecha = Carbon::parse($request->input('fecha_cita'))->startOfDay();
        $hoy = Carbon::now()->startOfDay();
        if ($fecha->lt($hoy)) {
            return back()->with('error', 'No puedes agendar en fechas pasadas.')->withInput();
        }

        $inicioGlobal = Carbon::parse($request->input('fecha_cita') . ' ' . $request->input('hora_cita'));

        // ✅ Hoy: no horas anteriores a "ahora - tolerancia"
        if ($fecha->equalTo($hoy)) {
            $minPermitido = Carbon::now()->subMinutes(self::TOLERANCIA_MINUTOS);
            if ($inicioGlobal->lt($minPermitido)) {
                return back()->with('error', 'Selecciona una hora válida (no puede ser anterior a la hora actual).')->withInput();
            }
        }

        // items
        $items = collect($request->input('items', []))
            ->map(fn($it) => [
                'id_servicio' => (int) $it['id_servicio'],
                'id_empleado' => (int) $it['id_empleado'],
                'orden'       => (int) $it['orden'],
            ])
            ->sortBy('orden')
            ->values()
            ->map(function ($it, $i) { $it['orden'] = $i + 1; return $it; });

        $serviceIds = $items->pluck('id_servicio')->unique()->values();
        $serviciosDb = Servicio::whereIn('id_servicio', $serviceIds)->get()->keyBy('id_servicio');

        $duracionTotal = 0;
        $total = 0.0;

        $items = $items->map(function ($it) use ($serviciosDb, &$duracionTotal, &$total) {
            $s = $serviciosDb[$it['id_servicio']];
            $precioFinal = max(0, (float)$s->precio - (float)($s->descuento ?? 0));
            $dur = max(1, (int)($s->duracion_minutos ?? 0));

            $it['precio_snapshot'] = $precioFinal;
            $it['duracion_snapshot'] = $dur;

            $duracionTotal += $dur;
            $total += $precioFinal;

            return $it;
        });

        // dia_semana 1-7
        $diaSemana = Carbon::parse($request->input('fecha_cita'))->isoWeekday();

        $horarios = DB::table('servicio_horarios')
            ->whereIn('servicio_id', $serviceIds)
            ->where('dia_semana', $diaSemana)
            ->get(['servicio_id', 'hora_inicio', 'hora_fin'])
            ->groupBy('servicio_id');

        foreach ($serviceIds as $sid) {
            if (!$horarios->has($sid) || $horarios[$sid]->isEmpty()) {
                return back()->with('error', 'Uno de los servicios no tiene horario disponible para ese día.')->withInput();
            }
        }

        // cadena de horas
        $cursor = $inicioGlobal->copy();
        $itemsConHoras = [];

        foreach ($items as $it) {
            $dur = (int) $it['duracion_snapshot'];
            $ini = $cursor->copy();
            $fin = $cursor->copy()->addMinutes($dur);

            $okEnVentana = false;
            foreach ($horarios[$it['id_servicio']] as $h) {
                $winIni = Carbon::parse($request->input('fecha_cita') . ' ' . substr((string)$h->hora_inicio, 0, 8));
                $winFin = Carbon::parse($request->input('fecha_cita') . ' ' . substr((string)$h->hora_fin, 0, 8));

                if ($ini->gte($winIni) && $fin->lte($winFin)) {
                    $okEnVentana = true;
                    break;
                }
            }

            if (!$okEnVentana) {
                return back()->with('error', 'El horario seleccionado no está disponible para uno de los servicios.')->withInput();
            }

            $it['hora_inicio'] = $ini->format('H:i:s');
            $it['hora_fin'] = $fin->format('H:i:s');

            $itemsConHoras[] = $it;
            $cursor = $fin;
        }

        // choques por empleado (detalle)
        $empleadoIds = collect($itemsConHoras)->pluck('id_empleado')->unique()->values();
        $estadosBloqueantes = ['pendiente','confirmada'];

        $ocupadasDetalle = DB::table('cita_servicio as cs')
            ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
            ->whereDate('c.fecha_cita', $request->input('fecha_cita'))
            ->whereIn('cs.id_empleado', $empleadoIds)
            ->whereNotNull('cs.hora_inicio')
            ->whereNotNull('cs.hora_fin')
            ->get(['cs.id_empleado','cs.hora_inicio','cs.hora_fin','c.estado_cita']);

        foreach ($ocupadasDetalle as $oc) {
            if (!in_array($oc->estado_cita, $estadosBloqueantes, true)) continue;

            $ocIni = Carbon::parse($request->input('fecha_cita') . ' ' . substr((string)$oc->hora_inicio, 0, 8));
            $ocFin = Carbon::parse($request->input('fecha_cita') . ' ' . substr((string)$oc->hora_fin, 0, 8));

            foreach ($itemsConHoras as $bn) {
                if ((int)$bn['id_empleado'] !== (int)$oc->id_empleado) continue;

                $bnIni = Carbon::parse($request->input('fecha_cita') . ' ' . $bn['hora_inicio']);
                $bnFin = Carbon::parse($request->input('fecha_cita') . ' ' . $bn['hora_fin']);

                if ($bnIni->lt($ocFin) && $bnFin->gt($ocIni)) {
                    return back()->with('error', 'Ese horario ya no está disponible (choque de agenda).')->withInput();
                }
            }
        }

        $responsableId = $this->calcularEmpleadoResponsable($itemsConHoras);

        return DB::transaction(function () use ($request, $clienteId, $responsableId, $inicioGlobal, $duracionTotal, $itemsConHoras) {
            $data = [
                'cliente_id' => $clienteId,
                'empleado_id' => $responsableId,
                'fecha_cita' => $request->input('fecha_cita'),
                'hora_cita'  => $inicioGlobal->format('H:i:s'),
                'duracion_total_minutos' => $duracionTotal,
                'estado_cita' => 'pendiente',
                'observaciones' => $request->input('observaciones'),
            ];

            $cita = Cita::create($data);

            foreach ($itemsConHoras as $it) {
                DB::table('cita_servicio')->insert([
                    'id_cita'           => $cita->id_cita,
                    'id_servicio'       => (int)$it['id_servicio'],
                    'id_empleado'       => (int)$it['id_empleado'],
                    'precio_snapshot'   => (float)$it['precio_snapshot'],
                    'duracion_snapshot' => (int)$it['duracion_snapshot'],
                    'hora_inicio'       => $it['hora_inicio'],
                    'hora_fin'          => $it['hora_fin'],
                    'orden'             => (int)$it['orden'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            return redirect()->route('agendarcita.create')->with('success', 'Cita registrada como pendiente. Continúa a pago para confirmarla.');
        });
    }

    private function calcularEmpleadoResponsable(array $itemsConHoras): int
    {
        $items = collect($itemsConHoras)->sortBy('orden')->values();
        $n = $items->count();

        if ($n === 1) return (int) $items[0]['id_empleado'];

        if ($n === 2) {
            $a = $items[0]; $b = $items[1];
            $da = (int)$a['duracion_snapshot']; $db = (int)$b['duracion_snapshot'];
            if ($da === $db) return (int)$a['id_empleado'];
            return $da > $db ? (int)$a['id_empleado'] : (int)$b['id_empleado'];
        }

        $agg = $items->groupBy('id_empleado')->map(fn($g) => [
            'count' => $g->count(),
            'dur_total' => $g->sum('duracion_snapshot'),
            'min_orden' => $g->min('orden'),
        ]);

        $best = $agg->sort(function ($x, $y) {
            if ($x['count'] !== $y['count']) return $y['count'] <=> $x['count'];
            if ($x['dur_total'] !== $y['dur_total']) return $y['dur_total'] <=> $x['dur_total'];
            return $x['min_orden'] <=> $y['min_orden'];
        })->keys()->first();

        return (int)$best;
    }

    // ✅ AJUSTE: coincide con tu ruta /agendar-cita/horas-disponibles
// Devuelve horas de INICIO (HH:MM) válidas para la combinación:
// fecha + items (servicios en orden) + empleados seleccionados.
public function horasDisponibles(Request $request)
{
    $fechaYmd = (string) $request->query('fecha', '');
    if (!$fechaYmd) {
        return response()->json(['ok' => true, 'horas' => []]);
    }

    try {
        $fecha = Carbon::createFromFormat('Y-m-d', $fechaYmd)->startOfDay();
    } catch (\Throwable $e) {
        return response()->json(['ok' => true, 'horas' => []]);
    }

    // No fechas pasadas
    if ($fecha->lt(Carbon::now()->startOfDay())) {
        return response()->json(['ok' => true, 'horas' => []]);
    }

    $items = $this->parseItemsFromRequest($request); // Collection of [id_servicio,id_empleado,orden]
    if ($items->isEmpty() || $items->contains(fn ($it) => empty($it['id_empleado']))) {
        return response()->json(['ok' => true, 'horas' => []]);
    }

    $serviceIds = $items->pluck('id_servicio')->unique()->values();

    // Duraciones
    $serviciosDb = Servicio::query()
        ->whereIn('id_servicio', $serviceIds)
        ->get(['id_servicio', 'duracion_minutos'])
        ->keyBy('id_servicio');

    // Si falta algún servicio, sin horas
    foreach ($serviceIds as $sid) {
        if (!$serviciosDb->has($sid)) {
            return response()->json(['ok' => true, 'horas' => []]);
        }
    }

    $itemsConDur = $items->map(function ($it) use ($serviciosDb) {
        $dur = max(1, (int) ($serviciosDb[$it['id_servicio']]->duracion_minutos ?? 0));
        $it['duracion'] = $dur;
        return $it;
    })->values();

    // Horarios del día (por servicio)
    $diaSemana = $fecha->isoWeekday(); // 1-7 (L-D)

    $horariosRows = DB::table('servicio_horarios')
        ->whereIn('servicio_id', $serviceIds)
        ->where('dia_semana', $diaSemana)
        ->get(['servicio_id', 'hora_inicio', 'hora_fin']);

    $horariosByService = [];
    foreach ($horariosRows as $r) {
        $sid = (int) $r->servicio_id;
        $horariosByService[$sid][] = [
            'ini' => substr((string) $r->hora_inicio, 0, 8),
            'fin' => substr((string) $r->hora_fin, 0, 8),
        ];
    }

    // Si algún servicio no tiene horario ese día, no hay horas
    foreach ($serviceIds as $sid) {
        if (empty($horariosByService[(int)$sid] ?? [])) {
            return response()->json(['ok' => true, 'horas' => []]);
        }
    }

    $windowsByService = $this->buildWindowsForDate($fechaYmd, $horariosByService);

    // Ocupadas por empleado ese día
    $empleadoIds = $itemsConDur->pluck('id_empleado')->unique()->values();
    $busyRows = DB::table('cita_servicio as cs')
        ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
        ->whereDate('c.fecha_cita', $fechaYmd)
        ->whereIn('cs.id_empleado', $empleadoIds)
        ->whereIn('c.estado_cita', ['pendiente', 'confirmada'])
        ->whereNotNull('cs.hora_inicio')
        ->whereNotNull('cs.hora_fin')
        ->get(['cs.id_empleado', 'cs.hora_inicio', 'cs.hora_fin']);

    $busyByEmpleado = $this->buildBusyForDate($fechaYmd, $busyRows);

    $horas = $this->computeSlotsForDate($fechaYmd, $itemsConDur->all(), $windowsByService, $busyByEmpleado);

    return response()->json(['ok' => true, 'horas' => $horas]);
}

// ✅ Endpoint mensual (para deshabilitar días sin disponibilidad)
// Espera: ?month=YYYY-MM + items[...] (mismo formato que horasDisponibles)
public function availabilityMonth(Request $request)
{
    $month = (string) $request->query('month', '');
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        return response()->json(['ok' => true, 'days' => new \stdClass()]);
    }

    $items = $this->parseItemsFromRequest($request);
    if ($items->isEmpty() || $items->contains(fn ($it) => empty($it['id_empleado']))) {
        return response()->json(['ok' => true, 'days' => new \stdClass()]);
    }

    $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end   = $start->copy()->endOfMonth();

    $serviceIds = $items->pluck('id_servicio')->unique()->values();

    // Duraciones
    $serviciosDb = Servicio::query()
        ->whereIn('id_servicio', $serviceIds)
        ->get(['id_servicio', 'duracion_minutos'])
        ->keyBy('id_servicio');

    foreach ($serviceIds as $sid) {
        if (!$serviciosDb->has($sid)) {
            return response()->json(['ok' => true, 'days' => new \stdClass()]);
        }
    }

    $itemsConDur = $items->map(function ($it) use ($serviciosDb) {
        $dur = max(1, (int) ($serviciosDb[$it['id_servicio']]->duracion_minutos ?? 0));
        $it['duracion'] = $dur;
        return $it;
    })->values();

    // Horarios del mes (todos los días de la semana) por servicio
    $horariosRows = DB::table('servicio_horarios')
        ->whereIn('servicio_id', $serviceIds)
        ->get(['servicio_id', 'dia_semana', 'hora_inicio', 'hora_fin']);

    $horariosByServiceDay = []; // [sid][dow] => [[ini,fin],...]
    foreach ($horariosRows as $r) {
        $sid = (int) $r->servicio_id;
        $dow = (int) $r->dia_semana;
        $horariosByServiceDay[$sid][$dow][] = [
            'ini' => substr((string) $r->hora_inicio, 0, 8),
            'fin' => substr((string) $r->hora_fin, 0, 8),
        ];
    }

    // Ocupadas del mes por empleado y por fecha
    $empleadoIds = $itemsConDur->pluck('id_empleado')->unique()->values();

    $busyRows = DB::table('cita_servicio as cs')
        ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
        ->whereBetween('c.fecha_cita', [$start->toDateString(), $end->toDateString()])
        ->whereIn('cs.id_empleado', $empleadoIds)
        ->whereIn('c.estado_cita', ['pendiente', 'confirmada'])
        ->whereNotNull('cs.hora_inicio')
        ->whereNotNull('cs.hora_fin')
        ->get(['c.fecha_cita', 'cs.id_empleado', 'cs.hora_inicio', 'cs.hora_fin']);

    $busyByDate = []; // [Y-m-d][emp] => [[ini,fin],...]
    foreach ($busyRows as $r) {
        $ymd = Carbon::parse($r->fecha_cita)->toDateString();
        $eid = (int) $r->id_empleado;
        $busyByDate[$ymd][$eid][] = [
            'ini' => substr((string) $r->hora_inicio, 0, 8),
            'fin' => substr((string) $r->hora_fin, 0, 8),
        ];
    }

    $days = [];

    for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
        $ymd = $d->toDateString();
        $dow = $d->isoWeekday();

        // horarios de ese día
        $horariosByService = [];
        $hasAll = true;
        foreach ($serviceIds as $sid) {
            $wins = $horariosByServiceDay[(int)$sid][$dow] ?? [];
            if (empty($wins)) { $hasAll = false; }
            $horariosByService[(int)$sid] = $wins;
        }

        if (!$hasAll) {
            $days[$ymd] = ['disabled' => true, 'slots' => 0];
            continue;
        }

        $windowsByService = $this->buildWindowsForDate($ymd, $horariosByService);

        // busy del día (convertido a Carbon)
        $busyForDayRows = [];
        if (!empty($busyByDate[$ymd] ?? [])) {
            foreach ($busyByDate[$ymd] as $eid => $list) {
                foreach ($list as $w) {
                    $busyForDayRows[] = (object) [
                        'id_empleado' => $eid,
                        'hora_inicio' => $w['ini'],
                        'hora_fin'    => $w['fin'],
                    ];
                }
            }
        }
        $busyByEmpleado = $this->buildBusyForDate($ymd, $busyForDayRows);

        $slots = $this->computeSlotsForDate($ymd, $itemsConDur->all(), $windowsByService, $busyByEmpleado);
        $days[$ymd] = [
            'disabled' => count($slots) === 0,
            'slots'    => count($slots),
        ];
    }

    return response()->json(['ok' => true, 'days' => $days]);
}

// =========================
// Helpers
// =========================

private function parseItemsFromRequest(Request $request)
{
    $raw = $request->input('items', []);
    if (!is_array($raw)) return collect();

    return collect($raw)
        ->map(function ($it) {
            return [
                'id_servicio' => (int) ($it['id_servicio'] ?? 0),
                'id_empleado' => ((int) ($it['id_empleado'] ?? 0)) ?: null,
                'orden'       => (int) ($it['orden'] ?? 0),
            ];
        })
        ->filter(fn ($it) => $it['id_servicio'] > 0)
        ->sortBy(fn ($it) => $it['orden'] > 0 ? $it['orden'] : 9999)
        ->values()
        ->map(function ($it, $i) {
            $it['orden'] = $i + 1;
            return $it;
        });
}

private function ceilToStep(Carbon $dt, int $stepMinutes): Carbon
{
    $dt = $dt->copy();
    $minute = (int) $dt->format('i');
    $second = (int) $dt->format('s');

    $mod = $minute % $stepMinutes;

    // si ya está alineado (y sin segundos), no mover
    if ($mod === 0 && $second === 0) {
        return $dt->second(0);
    }

    $add = $stepMinutes - $mod;
    $dt->second(0)->addMinutes($add);
    return $dt;
}

// Convierte horarios tipo ['sid' => [['ini'=>'09:00:00','fin'=>'17:00:00'],...]] a Carbon windows
private function buildWindowsForDate(string $fechaYmd, array $horariosByService): array
{
    $out = [];
    foreach ($horariosByService as $sid => $wins) {
        $sid = (int) $sid;
        $out[$sid] = [];
        foreach ($wins as $w) {
            $ini = Carbon::parse($fechaYmd . ' ' . substr((string)($w['ini'] ?? ''), 0, 8));
            $fin = Carbon::parse($fechaYmd . ' ' . substr((string)($w['fin'] ?? ''), 0, 8));

            // ignora ventanas inválidas
            if ($fin->lte($ini)) continue;

            $out[$sid][] = [$ini, $fin];
        }
    }
    return $out;
}

// Convierte rows con id_empleado,hora_inicio,hora_fin a Carbon intervals por empleado
private function buildBusyForDate(string $fechaYmd, $rows): array
{
    $busy = [];
    foreach ($rows as $r) {
        $eid = (int) ($r->id_empleado ?? 0);
        if (!$eid) continue;

        $iniS = substr((string) ($r->hora_inicio ?? ''), 0, 8);
        $finS = substr((string) ($r->hora_fin ?? ''), 0, 8);
        if (!$iniS || !$finS) continue;

        $ini = Carbon::parse($fechaYmd . ' ' . $iniS);
        $fin = Carbon::parse($fechaYmd . ' ' . $finS);
        if ($fin->lte($ini)) continue;

        $busy[$eid][] = [$ini, $fin];
    }
    return $busy;
}

private function isCandidateOk(Carbon $start, array $items, array $windowsByService, array $busyByEmpleado): bool
{
    $cursor = $start->copy();

    foreach ($items as $it) {
        $sid = (int) ($it['id_servicio'] ?? 0);
        $eid = (int) ($it['id_empleado'] ?? 0);
        $dur = (int) ($it['duracion'] ?? 0);

        if (!$sid || !$eid || $dur <= 0) return false;

        $ini = $cursor->copy();
        $fin = $cursor->copy()->addMinutes($dur);

        // 1) dentro de ventana del servicio
        $okWin = false;
        foreach (($windowsByService[$sid] ?? []) as $win) {
            [$wIni, $wFin] = $win;
            if ($ini->gte($wIni) && $fin->lte($wFin)) { $okWin = true; break; }
        }
        if (!$okWin) return false;

        // 2) no chocar con ocupado del empleado
        foreach (($busyByEmpleado[$eid] ?? []) as $busy) {
            [$bIni, $bFin] = $busy;
            if ($ini->lt($bFin) && $fin->gt($bIni)) {
                return false;
            }
        }

        $cursor = $fin;
    }

    return true;
}

private function computeSlotsForDate(string $fechaYmd, array $items, array $windowsByService, array $busyByEmpleado): array
{
    $dayStart = Carbon::parse($fechaYmd . ' 00:00:00');
    $dayEnd   = Carbon::parse($fechaYmd . ' 23:59:59');

    // Hoy: no horas anteriores a "ahora - tolerancia"
    $hoy = Carbon::now()->startOfDay();
    if ($dayStart->equalTo($hoy)) {
        $minPermitido = Carbon::now()->subMinutes(self::TOLERANCIA_MINUTOS);
        $dayStart = $this->ceilToStep($minPermitido, self::SLOT_STEP_MINUTOS);

        if ($dayStart->gt($dayEnd)) {
            return [];
        }
    } else {
        // Alinea al step
        $dayStart = $this->ceilToStep($dayStart, self::SLOT_STEP_MINUTOS);
    }

    $slots = [];
    $cursor = $dayStart->copy();

    $maxIter = (int) ceil((24 * 60) / self::SLOT_STEP_MINUTOS) + 2; // ~98
    for ($i = 0; $i < $maxIter && $cursor->lte($dayEnd); $i++) {
        if ($this->isCandidateOk($cursor, $items, $windowsByService, $busyByEmpleado)) {
            $slots[] = $cursor->format('H:i');
        }
        $cursor->addMinutes(self::SLOT_STEP_MINUTOS);
    }

    // unique por si acaso
    return array_values(array_unique($slots));
}

}
