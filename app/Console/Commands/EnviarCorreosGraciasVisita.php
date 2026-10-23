<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\EnviaCorreosPromocion;
use App\Mail\GraciasVisitaMail;
use App\Models\Cita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnviarCorreosGraciasVisita extends Command
{
    use EnviaCorreosPromocion;

    private const DIAS_ENTRE_CORREOS = 60;

    protected $signature = 'correos:gracias-visita
                            {--prueba : Solo muestra a quién se enviaría, sin enviar}
                            {--limite=80 : Máximo de correos por ejecución}
                            {--dias=3 : Revisa citas completadas de los últimos N días}';

    protected $description = 'Agradece la visita e invita a dejar reseña en Google Maps a quienes completaron su cita';

    public function handle(): int
    {
        $prueba = (bool) $this->option('prueba');
        $limite = (int) $this->option('limite');

        // Solo días anteriores: la cita de hoy recibe su correo mañana.
        $citas = Cita::with(['cliente', 'servicios'])
            ->whereIn('estado_cita', ['completada', 'finalizada'])
            ->whereDate('fecha_cita', '>=', today()->subDays((int) $this->option('dias')))
            ->whereDate('fecha_cita', '<', today())
            ->whereNotExists(function ($q) {
                $q->from('correos_enviados')
                  ->whereRaw('correos_enviados.referencia = CAST(citas.id_cita AS CHAR)')
                  ->where('tipo', 'gracias_visita');
            })
            ->orderBy('fecha_cita')
            ->get();

        $this->info("Citas completadas por agradecer: {$citas->count()}" . ($prueba ? ' (prueba)' : ''));

        $enviados = 0;
        $vistos   = [];
        foreach ($citas as $cita) {
            if ($enviados >= $limite) break;

            $cliente = $cita->cliente;
            if (!$cliente || !$cliente->aceptaPromociones() || isset($vistos[$cliente->id])) continue;
            $vistos[$cliente->id] = true;

            $recibioHacePoco = DB::table('correos_enviados')
                ->where('cliente_id', $cliente->id)
                ->where('tipo', 'gracias_visita')
                ->where('enviado_at', '>=', now()->subDays(self::DIAS_ENTRE_CORREOS))
                ->exists();
            if ($recibioHacePoco) continue;

            $servicios = $cita->servicios->pluck('nombre_servicio')->filter()->implode(' y ');

            $ok = $this->enviarUnaVez(
                $cliente,
                'gracias_visita',
                (string) $cita->id_cita,
                fn () => new GraciasVisitaMail($this->primerNombre($cliente), $servicios, $cliente->id),
                $prueba
            );
            $enviados += $ok ? 1 : 0;
        }

        $this->info("Listo: {$enviados} correos.");
        return self::SUCCESS;
    }
}
