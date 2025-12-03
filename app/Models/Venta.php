<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'id_cita',
        'total',
        'forma_pago',
        'metodo_pago_especifico',
        'referencia_pago',
        'notas',
        'comision_empleado'
    ];
    
    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
        'comision_empleado' => 'decimal:2'
    ];

    /**
     * Relación con la cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    /**
     * Relación con el servicio a través de la cita
     */
    public function servicio()
    {
        return $this->hasOneThrough(
            Servicio::class,
            Cita::class,
            'id_cita', // Foreign key on Cita table
            'id_servicio', // Foreign key on Servicio table
            'id_cita', // Local key on Venta table
            'id_servicio' // Local key on Cita table
        );
    }

    /**
     * Relación con el cliente a través de la cita
     */
    public function cliente()
    {
        return $this->hasOneThrough(
            User::class, // Asumo que usas User para clientes
            Cita::class,
            'id_cita',
            'id',
            'id_cita',
            'id_cliente'
        );
    }

    /**
     * Relación con el empleado a través de la cita
     */
    public function empleado()
    {
        return $this->hasOneThrough(
            User::class, // Asumo que usas User para empleados
            Cita::class,
            'id_cita',
            'id',
            'id_cita',
            'id_empleado'
        );
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar por forma de pago
     */
    public function scopePorFormaPago($query, $formaPago)
    {
        return $query->where('forma_pago', $formaPago);
    }
}