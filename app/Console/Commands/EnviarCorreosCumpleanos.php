<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\EnviaCorreosPromocion;
use App\Mail\CumpleanosMail;
use App\Models\Cliente;
use App\Models\Cupon;
use Illuminate\Console\Command;

class EnviarCorreosCumpleanos extends Command
{
    use EnviaCorreosPromocion;

    protected $signature = 'correos:cumpleanos
                            {--prueba : Solo muestra a quién se enviaría, sin enviar}
                            {--limite=80 : Máximo de correos por ejecución}';

    protected $description = 'Envía el regalo de cumpleaños a las clientas que cumplen años este mes (una vez al año)';

    public function handle(): int
    {
        $hoy    = now();
        $anio   = (string) $hoy->year;
        $prueba = (bool) $this->option('prueba');

        $clientes = Cliente::aceptanPromociones()
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', $hoy->month)
            // Mismo candado que el cupón: si la fecha es reciente, el regalo no aplicaría.
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_nacimiento_registrada_at')
                  ->orWhere('fecha_nacimiento_registrada_at', '<=', $hoy->copy()->subDays(Cupon::DIAS_ANTIGUEDAD_CUMPLEANOS));
            })
            ->whereNotExists(function ($q) use ($anio) {
                $q->from('correos_enviados')
                  ->whereColumn('correos_enviados.cliente_id', 'clientes.id')
                  ->where('tipo', 'cumpleanos')
                  ->where('referencia', $anio);
            })
            ->orderBy('id')
            ->limit((int) $this->option('limite'))
            ->get();

        $this->info("Cumpleaños de {$hoy->translatedFormat('F')}: {$clientes->count()} por enviar" . ($prueba ? ' (prueba)' : ''));

        $enviados = 0;
        foreach ($clientes as $cliente) {
            $ok = $this->enviarUnaVez(
                $cliente,
                'cumpleanos',
                $anio,
                fn () => new CumpleanosMail($this->primerNombre($cliente), $cliente->id),
                $prueba
            );
            $enviados += $ok ? 1 : 0;
        }

        $this->info("Listo: {$enviados} correos.");
        return self::SUCCESS;
    }
}
