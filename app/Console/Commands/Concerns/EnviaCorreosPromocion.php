<?php

namespace App\Console\Commands\Concerns;

use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait EnviaCorreosPromocion
{
    // Pausa entre correos para no rebasar los límites de envío de Hostinger.
    protected int $pausaMs = 1500;

    protected function primerNombre(Cliente $cliente): string
    {
        $nombre = trim((string) $cliente->nombre);
        return $nombre === '' ? 'Hola' : explode(' ', $nombre)[0];
    }

    /**
     * Registra el envío antes de mandar: si dos ejecuciones coinciden, el índice único
     * impide que la misma clienta reciba el correo dos veces.
     */
    protected function enviarUnaVez(Cliente $cliente, string $tipo, string $referencia, callable $crearCorreo, bool $prueba): bool
    {
        if ($prueba) {
            $this->line("  [prueba] {$cliente->email} ({$cliente->nombre})");
            return true;
        }

        try {
            $registroId = DB::table('correos_enviados')->insertGetId([
                'cliente_id' => $cliente->id,
                'tipo'       => $tipo,
                'referencia' => $referencia,
                'email'      => $cliente->email,
                'enviado_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $e) {
            return false;
        }

        try {
            /** @var Mailable $correo */
            $correo = $crearCorreo();
            Mail::to($cliente->email)->send($correo);
            $this->line("  enviado: {$cliente->email}");
            usleep($this->pausaMs * 1000);
            return true;
        } catch (\Throwable $e) {
            // Se borra el registro para reintentar en la siguiente ejecución.
            DB::table('correos_enviados')->where('id', $registroId)->delete();
            Log::warning("Correo {$tipo} no enviado a {$cliente->email}: " . $e->getMessage());
            $this->error("  error con {$cliente->email}: " . $e->getMessage());
            return false;
        }
    }
}
