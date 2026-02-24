<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ServiciosPublicController extends Controller
{
    public function index()
    {
        // Categorías + servicios (listado completo)
        $categorias = CategoriaServicio::query()
            ->where('estado', 'activo')
            ->with(['servicios' => function ($q) {
                $q->where('estado', 'activo')
                  ->orderBy('nombre_servicio');
            }])
            ->orderBy('nombre')
            ->get();

        // Para "más nuevo": si no hay created_at, usamos id_servicio
        $orderCol = Schema::hasColumn('servicios', 'created_at') ? 'created_at' : 'id_servicio';

        // =========================
        // 1) ✅ Servicio MÁS NUEVO (solo 1)
        // =========================
        $servicioNuevo = Cache::remember('servicio_nuevo_public', now()->addMinutes(30), function () use ($orderCol) {
            return Servicio::query()
                ->where('estado', 'activo')
                ->orderByDesc($orderCol)
                ->first([
                    'id_servicio',
                    'id_categoria',
                    'nombre_servicio',
                    'descripcion',
                    'duracion_minutos',
                    'precio',
                    'descuento',
                    'imagen',
                    'created_at',
                ]);
        });

        // =========================
        // 2) ✅ TOP 4 MÁS SOLICITADOS (mín 1 si hay datos)
        //    (usamos "del mes" como ya lo tenías)
        // =========================
        $month = now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $cacheKey = "top_servicios_public:$month";
        $ttl = $end->copy()->addHours(2);

        $topServicios = Cache::remember($cacheKey, $ttl, function () use ($start, $end) {
            return DB::table('cita_servicio as cs')
                ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
                ->join('servicios as s', 's.id_servicio', '=', 'cs.id_servicio')
                ->whereBetween('c.fecha_cita', [$start->toDateString(), $end->toDateString()])
                ->whereIn('c.estado_cita', ['pendiente', 'confirmada', 'completada']) // ajusta si tus estados son otros
                ->select(
                    's.id_servicio',
                    's.id_categoria',
                    's.nombre_servicio',
                    's.descripcion',
                    's.duracion_minutos',
                    's.precio',
                    's.descuento',
                    's.imagen',
                    DB::raw('COUNT(*) as total_reservas')
                )
                ->groupBy(
                    's.id_servicio',
                    's.id_categoria',
                    's.nombre_servicio',
                    's.descripcion',
                    's.duracion_minutos',
                    's.precio',
                    's.descuento',
                    's.imagen'
                )
                ->orderByDesc('total_reservas')
                ->limit(4)
                ->get();
        });

        // ✅ Fallback: si no hay reservas este mes, muestra 1–4 más nuevos (para cumplir "mínimo 1")
        if (($topServicios ?? collect())->isEmpty()) {
            $fallback = Servicio::query()
                ->where('estado', 'activo')
                ->orderByDesc($orderCol)
                ->take(4)
                ->get([
                    'id_servicio',
                    'id_categoria',
                    'nombre_servicio',
                    'descripcion',
                    'duracion_minutos',
                    'precio',
                    'descuento',
                    'imagen',
                    'created_at',
                ])
                ->map(function ($s) {
                    return (object) [
                        'id_servicio'      => $s->id_servicio,
                        'id_categoria'     => $s->id_categoria ?? null,
                        'nombre_servicio'  => $s->nombre_servicio,
                        'descripcion'      => $s->descripcion ?? null,
                        'duracion_minutos' => $s->duracion_minutos ?? null,
                        'precio'           => $s->precio ?? null,
                        'descuento'        => $s->descuento ?? null,
                        'imagen'           => $s->imagen ?? null,
                        'total_reservas'   => 0,
                    ];
                });

            $topServicios = $fallback;
        }

        return view('servicio', compact('categorias', 'servicioNuevo', 'topServicios'));
    }
}
