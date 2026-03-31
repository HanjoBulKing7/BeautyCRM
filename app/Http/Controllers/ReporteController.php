<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Tab principal del módulo (dashboard/servicios/productividad/asistencia/citas/ventas)
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
            'fecha' => $fechaNormalizada,
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
        $startFull = $inicio->toDateTimeString();
        $endFull   = $fin->toDateTimeString();

        // ✅ Validación mínima (tablas base)
        if (!Schema::hasTable('citas')) {
            return ['ok' => false, 'mensaje' => 'No existe la tabla "citas".'];
        }

        // Columnas reales (tu esquema)
        $citaPk      = 'id_cita';
        $fechaCol    = 'fecha_cita';
        $horaCol     = 'hora_cita';
        $estadoCol   = Schema::hasColumn('citas', 'estado_cita') ? 'estado_cita' : null;
        $empleadoCol = Schema::hasColumn('citas', 'id_empleado') ? 'id_empleado' : null;
        $pagoCol     = Schema::hasColumn('citas', 'metodo_pago') ? 'metodo_pago' : null;
        $descCol     = Schema::hasColumn('citas', 'descuento') ? 'descuento' : null;

        // Estados que cuentan como "venta realizada"
        $estadosRealizados = ['completada', 'COMPLETADA', 'finalizada', 'FINALIZADA'];

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
                ->whereIn($estadoCol, ['cancelada', 'CANCELADA'])
                ->count();
        }

        // ==========================
        // 2) Subquery por CITA (subtotal por cita usando pivot + snapshots)
        //    IMPORTANTE: ya NO usamos c.id_servicio porque ya no existe
        // ==========================
        $pivotExiste = Schema::hasTable('cita_servicio');
        $serviciosExiste = Schema::hasTable('servicios');

        $sub = DB::table('citas as c');

        if ($pivotExiste) {
            $sub->leftJoin('cita_servicio as cs', 'cs.id_cita', '=', "c.$citaPk");
        } else {
            // Si no existe pivot, no podemos calcular subtotal correctamente.
            // Igual dejamos el query armado para que no truene, pero subtotal será 0.
            $sub->leftJoin(DB::raw('(select 0 as id_cita) as cs'), DB::raw('1'), '=', DB::raw('0'));
        }

        if ($pivotExiste && $serviciosExiste) {
            $sub->leftJoin('servicios as sp', 'sp.id_servicio', '=', 'cs.id_servicio');
        }

        // Expresión de subtotal:
        // - prioridad: SUM(cs.precio_snapshot)
        // - fallback: SUM(sp.precio)
        // - si no hay pivot/servicios: 0
        $subtotalExpr = ($pivotExiste && $serviciosExiste)
            ? "COALESCE(SUM(cs.precio_snapshot), SUM(sp.precio), 0)"
            : "0";

        $sub = $sub
            ->whereBetween("c.$fechaCol", [$startDate, $endDate])
            ->selectRaw("
                c.$citaPk as id_cita,
                c.$fechaCol as fecha_cita,
                c.$horaCol as hora_cita,
                " . ($empleadoCol ? "c.$empleadoCol as id_empleado," : "NULL as id_empleado,") . "
                " . ($pagoCol ? "c.$pagoCol as metodo_pago," : "NULL as metodo_pago,") . "
                " . ($estadoCol ? "c.$estadoCol as estado_cita," : "NULL as estado_cita,") . "
                " . ($descCol ? "COALESCE(c.$descCol,0) as descuento," : "0 as descuento,") . "
                $subtotalExpr as subtotal
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
        // 3) Totales "ventas" para KPIs (preferir tabla ventas si existe)
        // ==========================

        // Fallback: cálculo por citas+pivot (por si no hay tabla ventas o no hay registros)
        $aggCitas = (clone $ventasBase)
            ->selectRaw("
                COUNT(*) as total_ventas,
                COALESCE(SUM(GREATEST(x.subtotal - x.descuento, 0)),0) as monto_total,
                COALESCE(AVG(GREATEST(x.subtotal - x.descuento, 0)),0) as ticket_promedio
            ")
            ->first();

        $montoTotal     = (float) ($aggCitas->monto_total ?? 0);
        $totalVentas    = (int)   ($aggCitas->total_ventas ?? 0);
        $ticketPromedio = (float) ($aggCitas->ticket_promedio ?? 0);

        // Fuente preferida: tabla ventas (cuadra con métodos de pago + gráfica)
        if (Schema::hasTable('ventas')) {
            $qVentas = DB::table('ventas as v')
                ->whereBetween('v.fecha_venta', [$inicio, $fin]);

            // Si existe estado_venta, filtramos a las pagadas (ajusta si manejas otros estados)
            if (Schema::hasColumn('ventas', 'estado_venta')) {
                $qVentas->whereIn('v.estado_venta', ['pagada', 'PAGADA']);
            }

            $aggVentas = $qVentas
                ->selectRaw("
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(v.total),0) as monto_total
                ")
                ->first();

            $ventasCount = (int) ($aggVentas->total_ventas ?? 0);

            // Si hay ventas en el rango, sobreescribimos KPIs con esta fuente
            if ($ventasCount > 0) {
                $totalVentas = $ventasCount;
                $montoTotal  = (float) ($aggVentas->monto_total ?? 0);
                $ticketPromedio = $totalVentas > 0 ? ($montoTotal / $totalVentas) : 0;
            }
        }
        // ==========================
        // 4) Métodos de pago (desde ventas)
        // ==========================
        $metodosPago = collect();
        if (Schema::hasTable('ventas')) {
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

            $labelsPago = [
                'efectivo'        => 'Efectivo',
                'transferencia'   => 'Transferencia',
                'tarjeta_credito' => 'Tarjeta crédito',
                'tarjeta_debito'  => 'Tarjeta débito',
                'tarjeta'         => 'Tarjeta',
                'mixto'           => 'Mixto',
                'sin_definir'     => 'Sin definir',
            ];

            $metodosPago = $metodosPago->map(function ($r) use ($labelsPago) {
                $key = (string) ($r->metodo ?? 'sin_definir');
                $r->metodo_label = $labelsPago[$key] ?? Str::title(str_replace('_', ' ', $key));
                return $r;
            });
        }

        // ==========================
        // 4.B) Resumen para gráfica (3 barras) + extras (mixto/otros)
        // ==========================
        $bucket = [
            'efectivo' => ['monto' => 0.0, 'cantidad' => 0],
            'transferencia' => ['monto' => 0.0, 'cantidad' => 0],
            'tarjeta' => ['monto' => 0.0, 'cantidad' => 0],
            'mixto' => ['monto' => 0.0, 'cantidad' => 0],
            'otros' => ['monto' => 0.0, 'cantidad' => 0],
        ];

        foreach ($metodosPago as $row) {
            $metodo = (string) ($row->metodo ?? 'sin_definir');
            $monto  = (float) ($row->monto ?? 0);
            $cant   = (int)   ($row->cantidad ?? 0);

            if ($metodo === 'efectivo') {
                $bucket['efectivo']['monto'] += $monto;
                $bucket['efectivo']['cantidad'] += $cant;
                continue;
            }

            if ($metodo === 'transferencia') {
                $bucket['transferencia']['monto'] += $monto;
                $bucket['transferencia']['cantidad'] += $cant;
                continue;
            }

            if ($metodo === 'tarjeta_credito' || $metodo === 'tarjeta_debito' || $metodo === 'tarjeta') {
                $bucket['tarjeta']['monto'] += $monto;
                $bucket['tarjeta']['cantidad'] += $cant;
                continue;
            }

            if ($metodo === 'mixto') {
                $bucket['mixto']['monto'] += $monto;
                $bucket['mixto']['cantidad'] += $cant;
                continue;
            }

            $bucket['otros']['monto'] += $monto;
            $bucket['otros']['cantidad'] += $cant;
        }

        $resumenPagos = [
            'efectivo' => $bucket['efectivo'],
            'transferencia' => $bucket['transferencia'],
            'tarjeta' => $bucket['tarjeta'],
            'mixto' => $bucket['mixto'],
            'otros' => $bucket['otros'],
            'total' => [
                'monto' => ($bucket['efectivo']['monto'] + $bucket['transferencia']['monto'] + $bucket['tarjeta']['monto'] + $bucket['mixto']['monto'] + $bucket['otros']['monto']),
                'cantidad' => ($bucket['efectivo']['cantidad'] + $bucket['transferencia']['cantidad'] + $bucket['tarjeta']['cantidad'] + $bucket['mixto']['cantidad'] + $bucket['otros']['cantidad']),
            ],
        ];

        $resumenChart = [
            'labels' => ['Efectivo', 'Transferencia', 'Tarjeta'],
            'values' => [
                round($bucket['efectivo']['monto'], 2),
                round($bucket['transferencia']['monto'], 2),
                round($bucket['tarjeta']['monto'], 2),
            ],
        ];

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
        $topServicios = collect();
        if (Schema::hasTable('cita_servicio') && Schema::hasTable('servicios') && Schema::hasTable('ventas')) {
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
        }

        // ==========================
        // 7) Últimas ventas (preferir tabla ventas)
        // ==========================
        $ultimasVentas = collect();

        if (Schema::hasTable('ventas')) {
            $ultimasVentas = DB::table('ventas as v')
                ->whereBetween('v.fecha_venta', [$inicio, $fin])
                ->orderByDesc('v.fecha_venta')
                ->limit(10)
                ->get([
                    'v.id_cita',
                    DB::raw('DATE(v.fecha_venta) as fecha_cita'),
                    DB::raw('TIME(v.fecha_venta) as hora_cita'),
                    DB::raw('COALESCE(v.total,0) as total'),
                    DB::raw("COALESCE(NULLIF(v.forma_pago,''),'sin_definir') as forma_pago"),
                ]);
        } else {
            // Fallback si no existe ventas
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
        }

        // ==========================
        // 8) Ventas de productos
        // ==========================
        $totalProductosMonto = 0;
        $totalProductosCant  = 0;
        $productosDetalle    = collect();

        if (Schema::hasTable('ventas_productos')) {
            $qProductos = DB::table('ventas_productos')
                ->whereBetween('created_at', [$startFull, $endFull]);

            $aggProductos = $qProductos->selectRaw("
                COUNT(*) as cantidad,
                COALESCE(SUM(total), 0) as monto
            ")->first();

            $totalProductosCant  = (int) ($aggProductos->cantidad ?? 0);
            $totalProductosMonto = (float) ($aggProductos->monto ?? 0);

            if (Schema::hasTable('producto_venta_producto')) {
                $productosDetalle = DB::table('producto_venta_producto as pvp')
                    ->join('ventas_productos as vp', 'vp.id', '=', 'pvp.venta_producto_id')
                    ->join('productos as p', 'p.id', '=', 'pvp.producto_id')
                    ->whereBetween('vp.created_at', [$startFull, $endFull])
                    ->selectRaw("
                        p.nombre,
                        SUM(pvp.cantidad) as total_vendido,
                        SUM(pvp.subtotal) as ingresos
                    ")
                    ->groupBy('p.id', 'p.nombre')
                    ->orderByDesc('total_vendido')
                    ->limit(5)
                    ->get();
            }
        }

        // ==========================
        // Clientes nuevos (tabla clientes)
        $clientesNuevos = 0;
        if (Schema::hasTable('clientes')) {
            $clientesNuevos = DB::table('clientes')
                ->whereBetween('created_at', [$inicio, $fin])
                ->count();
        }
        // ==========================
        // Últimos clientes (en el rango)
        // ==========================
        $ultimosClientes = collect();

        if (Schema::hasTable('clientes')) {
            $q = DB::table('clientes');
            $q->whereBetween('created_at', [$inicio, $fin]);

            // Select seguro según columnas existentes
            $select = ['id', 'user_id', 'nombre', 'email', 'telefono', 'direccion', 'created_at'];
            $ultimosClientes = $q->orderByDesc('created_at')->limit(10)->get($select);
        }


        $granTotalIngresos = $montoTotal + $totalProductosMonto;
        $totalVentasGeneral = $totalVentas + $totalProductosCant;

        return [
            'ok' => true,
            'ventas' => [
                'monto_servicios' => $montoTotal,
                'monto_productos' => $totalProductosMonto,
                'monto_total'     => $granTotalIngresos,
                'total_ventas'    => $totalVentasGeneral,
                'ticket_promedio' => $totalVentasGeneral > 0 ? ($granTotalIngresos / $totalVentasGeneral) : 0,
                'metodos_pago'    => $metodosPago,
                'ultimas'         => $ultimasVentas,
                'resumen_pagos'   => $resumenPagos,
                'resumen_chart'   => $resumenChart,
            ],
            'productos_stats' => [
                'total_monto' => $totalProductosMonto,
                'total_cantidad' => $totalProductosCant,
                'top' => $productosDetalle,
            ],
            'citas' => $citas,
            'empleados' => ['top' => $topEmpleados],
            'servicios' => ['top' => $topServicios],
            'clientes' => [
                'nuevos'  => $clientesNuevos,
                'ultimos' => $ultimosClientes,
            ],
        ];
    }

    // (helpers que ya traías; los dejo por si los usas después)
    private function guessCitasFechaColumn(): string
    {
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
