<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Tab principal del módulo (el que ya traes: dashboard/servicios/productividad/asistencia/citas/ventas)
        $tab = $request->query('tab', 'ventas');

        // Periodo (sub-tabs dentro de ventas)
        $tipo  = $request->query('tipo', 'diario');   // diario|semanal|mensual
        $fecha = $request->query('fecha', now()->toDateString());

        // Normaliza rango
        [$inicio, $fin, $fechaNormalizada] = $this->resolveRango($tipo, $fecha);

        $stats = [];
        if ($tab === 'ventas') {
            $stats = $this->buildVentasReport($inicio, $fin);
        }

        return view('admin.reportes.index', [
            'tab'   => $tab,
            'tipo'  => $tipo,
            'fecha' => $fechaNormalizada, // se usa en inputs
            'rango' => ['inicio' => $inicio, 'fin' => $fin],
            'stats' => $stats,
        ]);
    }

    private function resolveRango(string $tipo, string $fecha): array
    {
        $f = Carbon::parse($fecha);

        if ($tipo === 'mensual') {
            $inicio = $f->copy()->startOfMonth()->startOfDay();
            $fin    = $f->copy()->endOfMonth()->endOfDay();
            return [$inicio, $fin, $inicio->toDateString()];
        }

        if ($tipo === 'semanal') {
            // semana Lunes->Domingo
            $inicio = $f->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $fin    = $inicio->copy()->addDays(6)->endOfDay();
            return [$inicio, $fin, $inicio->toDateString()];
        }

        // diario
        $inicio = $f->copy()->startOfDay();
        $fin    = $f->copy()->endOfDay();
        return [$inicio, $fin, $inicio->toDateString()];
    }

    private function buildVentasReport(Carbon $inicio, Carbon $fin): array
    {
        $startDate = $inicio->toDateString();
        $endDate   = $fin->toDateString();

        // ✅ Validación mínima
        if (!Schema::hasTable('ventas')) {
            return ['ok' => false, 'mensaje' => 'No existe la tabla "citas".'];
        }

        // Columnas reales
        $citaPk     = 'id_cita';
        $fechaCol   = 'fecha_cita';
        $horaCol    = 'hora_cita';
        $estadoCol  = Schema::hasColumn('citas', 'estado_cita') ? 'estado_cita' : null;
        $empleadoCol= Schema::hasColumn('citas', 'id_empleado') ? 'id_empleado' : null;
        $pagoCol    = Schema::hasColumn('citas', 'metodo_pago') ? 'metodo_pago' : null;
        $descCol    = Schema::hasColumn('citas', 'descuento') ? 'descuento' : null;

        // Estados que cuentan como "venta realizada"
        $estadosRealizados = ['completada','COMPLETADA','finalizada','FINALIZADA'];

        // ==========================
        // 1) Stats de citas
        // ==========================
        $citas = [
            'total'       => DB::table('citas')->whereBetween($fechaCol, [$startDate, $endDate])->count(),
            'completadas' => 0,
            'canceladas'  => 0,
        ];

        if ($estadoCol) {
            $citas['completadas'] = DB::table('citas')
                ->whereBetween($fechaCol, [$startDate, $endDate])
                ->whereIn($estadoCol, $estadosRealizados)
                ->count();

            $citas['canceladas'] = DB::table('citas')
                ->whereBetween($fechaCol, [$startDate, $endDate])
                ->whereIn($estadoCol, ['cancelada','CANCELADA'])
                ->count();
        }

        // ==========================
        // 2) Subquery por CITA (subtotal por cita usando snapshots)
        //    - evita duplicar descuento por pivot
        // ==========================
        $hasPivot = Schema::hasTable('cita_servicio') && Schema::hasTable('servicios');

        $sub = DB::table('citas as c')
            ->leftJoin('cita_servicio as cs', 'cs.id_cita', '=', "c.$citaPk")
            ->leftJoin('servicios as sp', 'sp.id_servicio', '=', 'cs.id_servicio')  // servicio por pivot
            ->leftJoin('servicios as sc', 'sc.id_servicio', '=', 'c.id_servicio')   // fallback si no hay pivot
            ->whereBetween("c.$fechaCol", [$startDate, $endDate])
            ->selectRaw("
                c.$citaPk as id_cita,
                c.$fechaCol as fecha_cita,
                c.$horaCol as hora_cita,
                " . ($empleadoCol ? "c.$empleadoCol as id_empleado," : "NULL as id_empleado,") . "
                " . ($pagoCol ? "c.$pagoCol as metodo_pago," : "NULL as metodo_pago,") . "
                " . ($estadoCol ? "c.$estadoCol as estado_cita," : "NULL as estado_cita,") . "
                " . ($descCol ? "COALESCE(c.$descCol,0) as descuento," : "0 as descuento,") . "
                COALESCE(
                    SUM(cs.precio_snapshot),
                    SUM(sp.precio),
                    MAX(sc.precio),
                    0
                ) as subtotal
            ")
            ->groupBy(
                "c.$citaPk",
                "c.$fechaCol",
                "c.$horaCol",
                $empleadoCol ? "c.$empleadoCol" : DB::raw('id_empleado'),
                $pagoCol ? "c.$pagoCol" : DB::raw('metodo_pago'),
                $estadoCol ? "c.$estadoCol" : DB::raw('estado_cita'),
                $descCol ? "c.$descCol" : DB::raw('descuento')
            );

        $ventasBase = DB::query()->fromSub($sub, 'x');

        // Filtra a "realizadas" (si hay estado)
        if ($estadoCol) {
            $ventasBase->whereIn('x.estado_cita', $estadosRealizados);
        }

        // ==========================
        // 3) Totales "ventas" desde citas+pivot
        // ==========================
        $agg = (clone $ventasBase)
            ->selectRaw("
                COUNT(*) as total_ventas,
                COALESCE(SUM(GREATEST(x.subtotal - x.descuento, 0)),0) as monto_total,
                COALESCE(AVG(GREATEST(x.subtotal - x.descuento, 0)),0) as ticket_promedio
            ")
            ->first();

            $montoTotal     = (float) ($agg->monto_total ?? 0);
            $totalVentas    = (int) ($agg->total_ventas ?? 0);
            $ticketPromedio = (float) ($agg->ticket_promedio ?? 0);

        // ========================== 
        // 4) Métodos de pago (desde citas)
        // ==========================
        $metodosPago = DB::table('ventas as v')
            ->whereBetween('v.fecha_venta', [$inicio, $fin])
            ->selectRaw("
                COALESCE(v.forma_pago, 'sin_definir') as metodo,
                COUNT(*) as cantidad,
                COALESCE(SUM(v.total), 0) as monto
            ")
            ->groupBy('v.forma_pago')
            ->orderByDesc('cantidad')
            ->get();



        // ==========================
        // 5) Top empleados (desde citas)
        // ==========================
        $topEmpleados = collect();
        if ($empleadoCol && Schema::hasTable('users')) {
            $topEmpleados = (clone $ventasBase)
                ->leftJoin('users as u', 'u.id', '=', 'x.id_empleado')
                ->selectRaw("
                    x.id_empleado as empleado_id,
                    COALESCE(u.name,'(Sin nombre)') as empleado,
                    COUNT(*) as ventas,
                    COALESCE(SUM(GREATEST(x.subtotal - x.descuento,0)),0) as ingresos
                ")
                ->groupBy('x.id_empleado', 'u.name')
                ->orderByDesc('ingresos')
                ->limit(10)
                ->get();
        }

            // ==========================
            // 6) Top servicios (desde pivot)
            // ==========================
            $topServicios = DB::table('cita_servicio as cs')
                ->join('citas as c', 'c.id_cita', '=', 'cs.id_cita')
                ->join('ventas as v', 'v.id_cita', '=', 'c.id_cita')
                ->join('servicios as s', 's.id_servicio', '=', 'cs.id_servicio')
                ->whereBetween('v.fecha_venta', [$inicio, $fin])
                ->selectRaw("
                    s.id_servicio,
                    s.nombre_servicio as servicio,
                    COUNT(cs.id_servicio) as veces,
                    COALESCE(SUM(cs.precio_snapshot), 0) as ingresos_estimados
                ")
                ->groupBy('s.id_servicio', 's.nombre_servicio')
                ->orderByDesc('ingresos_estimados')
                ->limit(10)
                ->get();



        // ==========================
        // 7) Últimas "ventas" (últimas citas completadas)
        // ==========================
        $ultimasVentas = (clone $ventasBase)
            ->orderByDesc('x.fecha_cita')
            ->orderByDesc('x.hora_cita')
            ->limit(10)
            ->get([
                'x.id_cita',
                'x.fecha_cita',
                'x.hora_cita',
                DB::raw("GREATEST(x.subtotal - x.descuento,0) as total"),
                DB::raw("COALESCE(NULLIF(x.metodo_pago,''),'sin_definir') as forma_pago"),
            ]);

        // Clientes nuevos (sigues usando users role_id=1)
        $clientesNuevos = 0;
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            $clientesNuevos = DB::table('users')
                ->where('role_id', 1)
                ->whereBetween('created_at', [$inicio, $fin])
                ->count();
        }

        return [
            'ok' => true,
            'ventas' => [
                'monto_total'     => $montoTotal,
                'total_ventas'    => $totalVentas,
                'ticket_promedio' => $ticketPromedio,
                'metodos_pago'    => $metodosPago,
                'ultimas'         => $ultimasVentas,
            ],
            'citas' => $citas,
            'empleados' => ['top' => $topEmpleados],
            'servicios' => ['top' => $topServicios],
            'clientes' => ['nuevos' => $clientesNuevos],
        ];
    }


    private function guessCitasFechaColumn(): string
    {
        // Ajusta aquí si tu columna real se llama diferente
        $candidates = ['fecha', 'fecha_cita', 'fecha_hora', 'start', 'created_at'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('citas', $col)) return $col;
        }
        return 'created_at';
    }

    private function guessCitasStatusColumn(): ?string
    {
        $candidates = ['estado', 'status'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('citas', $col)) return $col;
        }
        return null;
    }

    private function guessCitasEmpleadoColumn(): ?string
    {
        $candidates = ['empleado_id', 'id_empleado', 'user_empleado_id'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('citas', $col)) return $col;
        }
        return null;
    }

    private function guessCitasPkColumn(): string
    {
        $candidates = ['id', 'id_cita'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('citas', $col)) return $col;
        }
        return 'id';
    }

    private function guessServiciosPkColumn(): string
    {
        $candidates = ['id', 'id_servicio'];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('servicios', $col)) return $col;
        }
        return 'id';
    }

    private function guessPivotCitaServicio(): ?string
    {
        $candidates = ['cita_servicio', 'cita_servicios', 'cita_servicio_detalle', 'cita_servicio_rel'];
        foreach ($candidates as $t) {
            if (Schema::hasTable($t)) return $t;
        }
        return null;
    }
}
