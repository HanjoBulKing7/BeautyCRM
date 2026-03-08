<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Incrementa el contador de usos.
     */
    public function incrementarUso(): void
    {
        $this->increment('usos_actuales');
    }
}
