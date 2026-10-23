<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Cupon extends Model
{
    use HasFactory;

    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'descuento_maximo',
        'monto_minimo',
        'fecha_inicio',
        'fecha_fin',
        'cantidad_usos',
        'usos_actuales',
        'cantidad_por_cliente',
        'aplica_cumpleaños',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_descuento' => 'decimal:2',
        'descuento_maximo' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'aplica_cumpleaños' => 'boolean',
    ];

    /**
     * Verifica si el cupón es válido para usarse.
     */
    public function esValido(): bool
    {
        // Verificar estado
        if ($this->estado !== 'activo') {
            return false;
        }

        // Verificar fechas
        $ahora = Carbon::now()->startOfDay();
        if ($this->fecha_inicio && $ahora->isBefore($this->fecha_inicio)) {
            return false;
        }
        if ($this->fecha_fin && $ahora->isAfter($this->fecha_fin)) {
            return false;
        }

        // Verificar cantidad de usos
        if ($this->cantidad_usos && $this->usos_actuales >= $this->cantidad_usos) {
            return false;
        }

        return true;
    }

    /**
     * Calcula el descuento a aplicar sobre un monto.
     */
    public function calcularDescuento(float $monto): float
    {
        if (!$this->esValido()) {
            return 0;
        }

        // Verificar monto mínimo
        if ($this->monto_minimo && $monto < $this->monto_minimo) {
            return 0;
        }

        $descuento = 0;

        if ($this->tipo_descuento === 'porcentaje') {
            $descuento = ($monto * $this->valor_descuento) / 100;

            // Aplicar máximo descuento si está definido
            if ($this->descuento_maximo) {
                $descuento = min($descuento, $this->descuento_maximo);
            }
        } else {
            // Tipo: monto fijo
            $descuento = $this->valor_descuento;
        }

        // No puede ser más del monto total
        return min((float) $descuento, $monto);
    }

    // Evita que alguien registre su cumpleaños el mismo mes solo para obtener el regalo.
    public const DIAS_ANTIGUEDAD_CUMPLEANOS = 30;

    /**
     * Valida el cupón contra la cuenta del cliente.
     * Devuelve null si aplica, o el motivo por el que no aplica.
     */
    public function motivoNoAplica(?Cliente $cliente, ?float $monto = null): ?string
    {
        if (!$this->esValido()) {
            return 'Este cupón ya expiró o no está activo.';
        }

        if (!$cliente) {
            return 'Inicia sesión para usar este cupón.';
        }

        $porCliente = max(1, (int) ($this->cantidad_por_cliente ?? 1));
        $usados = DB::table('cupon_usos')
            ->where('cupon_id', $this->id)
            ->where('cliente_id', $cliente->id)
            ->count();
        if ($usados >= $porCliente) {
            return 'Ya utilizaste este cupón en tu cuenta.';
        }

        if ($this->aplica_cumpleaños) {
            if (!$cliente->fecha_nacimiento) {
                return 'Registra tu fecha de nacimiento para usar tu regalo de cumpleaños.';
            }

            if (Carbon::parse($cliente->fecha_nacimiento)->month !== Carbon::now()->month) {
                return 'Este regalo solo es válido durante tu mes de cumpleaños.';
            }

            $registrada = $cliente->fecha_nacimiento_registrada_at;
            if ($registrada && Carbon::parse($registrada)->gt(Carbon::now()->subDays(self::DIAS_ANTIGUEDAD_CUMPLEANOS))) {
                return 'Tu fecha de nacimiento se registró hace poco; el regalo estará disponible en tu próximo cumpleaños.';
            }
        }

        if ($monto !== null && $this->monto_minimo && $monto < (float) $this->monto_minimo) {
            return 'El monto mínimo para este cupón es $' . number_format((float) $this->monto_minimo, 2) . '.';
        }

        return null;
    }

    public function registrarUso(int $clienteId, ?int $citaId, float $descuento): void
    {
        DB::table('cupon_usos')->insert([
            'cupon_id'   => $this->id,
            'cliente_id' => $clienteId,
            'id_cita'    => $citaId,
            'descuento'  => $descuento,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->incrementarUso();
    }

    /**
     * Incrementa el contador de usos.
     */
    public function incrementarUso(): void
    {
        $this->increment('usos_actuales');
    }
}
