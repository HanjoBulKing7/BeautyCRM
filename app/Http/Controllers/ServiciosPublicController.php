<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ServiciosPublicController extends Controller
{

public function index()
{
    $categorias = CategoriaServicio::query()
        ->where('estado', 'activo')
        ->with(['servicios' => function ($q) {
            $q->where('estado', 'activo')
              ->orderBy('nombre_servicio');
        }])
        ->orderBy('nombre')
        ->get();

    // ✅ Mes actual automático (sin selector)
    $month = now()->format('Y-m');
    $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end   = (clone $start)->endOfMonth();

    // ✅ Cache que cambia SOLO por mes
    $cacheKey = "top_servicios_public:$month";

    // ✅ TTL: hasta que termine el mes (+ 2 horas)
    $ttl = $end->copy()->addHours(2);

    $topServicios = Cache::remember($cacheKey, $ttl, function () use ($start, $end) {
        return DB::table('cita_servicio as cs')
            ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
            ->join('servicios as s', 's.id_servicio', '=', 'cs.id_servicio')
            ->whereBetween('c.fecha_cita', [$start->toDateString(), $end->toDateString()])
            // opcional: excluir canceladas
            // ->where('c.estado_cita', '!=', 'cancelada')
            ->select(
                's.id_servicio',
                's.nombre_servicio',
                DB::raw('COUNT(*) as total_reservas')
            )
            ->groupBy('s.id_servicio', 's.nombre_servicio')
            ->orderByDesc('total_reservas')
            ->limit(4)
            ->get();
    });

    return view('servicio', compact('categorias', 'topServicios'));
}

}
