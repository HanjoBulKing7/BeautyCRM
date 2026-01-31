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
        'fecha_venta',
        'total',
        'forma_pago',
        'estado_venta',
        'metodo_pago_especifico',
        'referencia_pago',
        'notas',
        'comision_empleado',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
        'comision_empleado' => 'decimal:2',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    // (Opcional) Helpers cómodos para no estar escribiendo ->cita->
    public function getClienteAttribute()
    {
        return $this->cita?->cliente;
    }

    public function getEmpleadoAttribute()
    {
        return $this->cita?->empleado;
    }

    public function getServiciosAttribute()
    {
        return $this->cita?->servicios ?? collect();
    }
}
