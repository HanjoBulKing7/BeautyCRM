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
    public function horasDisponibles(Request $request)
    {
        // Luego lo completamos con slots reales (si quieres ya lo hago en el siguiente paso)
        return response()->json(['ok' => true, 'horas' => []]);
    }

    // ✅ Endpoint mensual (para deshabilitar días llenos)
    public function availabilityMonth(Request $request)
    {
        return response()->json(['ok' => true, 'days' => new \stdClass()]);
    }
}
