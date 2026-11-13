<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Única regla de "empleada ocupada" para la reserva en línea y el admin.
 * Cada servicio de una cita ocupa a su empleada (o a la responsable si no tiene);
 * si el servicio no guardó sus horas (citas capturadas en el admin), ocupa toda la cita.
 */
class AgendaOcupacion
{
    public const ESTADOS_QUE_OCUPAN = ['pendiente', 'confirmada', 'completada'];

    /**
     * @return Collection<object{fecha:string,id_empleado:int,hora_inicio:string,hora_fin:string,id_cita:int}>
     */
    public function filas(string $desde, string $hasta, array $empleadoIds = [], ?int $excluirCitaId = null): Collection
    {
        $rows = DB::table('citas as c')
            ->leftJoin('cita_servicio as cs', 'cs.id_cita', '=', 'c.id_cita')
            ->whereBetween('c.fecha_cita', [$desde, $hasta])
            ->whereIn('c.estado_cita', self::ESTADOS_QUE_OCUPAN)
            ->when($excluirCitaId, fn ($q) => $q->where('c.id_cita', '!=', $excluirCitaId))
            ->get([
                'c.id_cita', 'c.fecha_cita', 'c.hora_cita', 'c.duracion_total_minutos', 'c.empleado_id',
                'cs.id_empleado', 'cs.hora_inicio', 'cs.hora_fin', 'cs.duracion_snapshot',
            ]);

        $duracionPorCita = $rows->groupBy('id_cita')->map(function ($g) {
            $total = (int) ($g->first()->duracion_total_minutos ?? 0);
            if ($total <= 0) $total = (int) $g->sum('duracion_snapshot');
            return max(15, $total ?: 60);
        });

        $filtro = array_map('intval', array_filter($empleadoIds));
        $out = collect();

        foreach ($rows as $r) {
            $emp = (int) ($r->id_empleado ?: $r->empleado_id);
            if (!$emp || ($filtro && !in_array($emp, $filtro, true))) continue;

            $fecha = Carbon::parse($r->fecha_cita)->toDateString();

            if ($r->hora_inicio && $r->hora_fin) {
                $ini = substr((string) $r->hora_inicio, 0, 8);
                $fin = substr((string) $r->hora_fin, 0, 8);
            } else {
                $inicio = Carbon::parse($fecha . ' ' . substr((string) $r->hora_cita, 0, 8));
                $ini = $inicio->format('H:i:s');
                $fin = $inicio->copy()->addMinutes($duracionPorCita[$r->id_cita])->min(Carbon::parse($fecha . ' 23:59:59'))->format('H:i:s');
            }

            $out->push((object) [
                'id_cita'     => (int) $r->id_cita,
                'fecha'       => $fecha,
                'id_empleado' => $emp,
                'hora_inicio' => $ini,
                'hora_fin'    => $fin,
            ]);
        }

        return $out;
    }

    /**
     * Reparte la cita en tramos seguidos (un servicio tras otro) desde la hora de inicio.
     * $items: [['id_empleado'=>int, 'duracion'=>int], ...] en orden.
     * @return array<int, array{id_empleado:int, hora_inicio:string, hora_fin:string}>
     */
    public function encadenar(string $fecha, string $hora, array $items): array
    {
        $cursor = Carbon::parse($fecha . ' ' . substr($hora, 0, 5));
        $tramos = [];
        foreach ($items as $it) {
            $fin = $cursor->copy()->addMinutes(max(1, (int) $it['duracion']));
            $tramos[] = [
                'id_empleado' => (int) $it['id_empleado'],
                'hora_inicio' => $cursor->format('H:i:s'),
                'hora_fin'    => $fin->format('H:i:s'),
            ];
            $cursor = $fin;
        }
        return $tramos;
    }

    /** Devuelve la primera ocupación que choca con los tramos, o null si está libre. */
    public function primerChoque(string $fecha, array $tramos, ?int $excluirCitaId = null): ?object
    {
        $emps = array_values(array_unique(array_filter(array_column($tramos, 'id_empleado'))));
        if (!$emps) return null;

        foreach ($this->filas($fecha, $fecha, $emps, $excluirCitaId) as $oc) {
            foreach ($tramos as $t) {
                if ((int) $t['id_empleado'] !== $oc->id_empleado) continue;
                if ($t['hora_inicio'] < $oc->hora_fin && $t['hora_fin'] > $oc->hora_inicio) {
                    return $oc;
                }
            }
        }
        return null;
    }

    /** Serializa reservas simultáneas de las mismas empleadas (evita duplicados por doble clic). Llamar dentro de una transacción. */
    public function bloquearEmpleadas(array $empleadoIds): void
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($empleadoIds))));
        if ($ids) {
            DB::table('empleados')->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get(['id']);
        }
    }
}
