<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'id_cita',
        'id_cliente',
        'id_empleado',
        'id_servicio',
        'fecha_venta',
        'subtotal',
        'descuento',
        'total',
        'forma_pago',
        'estado_venta',
        'observaciones'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    // Relaciones ajustadas para usar User
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'id_empleado');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita');
    }

    // Scopes para filtros
    public function scopeFecha($query, $fechaInicio, $fechaFin)
    {
        if ($fechaInicio && $fechaFin) {
            return $query->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        }
        return $query;
    }

    public function scopeEmpleado($query, $empleadoId)
    {
        if ($empleadoId) {
            return $query->where('id_empleado', $empleadoId);
        }
        return $query;
    }

    public function scopeServicio($query, $servicioId)
    {
        if ($servicioId) {
            return $query->where('id_servicio', $servicioId);
        }
        return $query;
    }

    public function scopeFormaPago($query, $formaPago)
    {
        if ($formaPago) {
            return $query->where('forma_pago', $formaPago);
        }
        return $query;
    }
}