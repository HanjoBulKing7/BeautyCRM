<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    protected $keyType = 'int';

    protected $fillable = [
        'nombre_servicio',
        'descripcion',
        'precio',
        'duracion_minutos',
        'estado',
        'imagen',
        'id_categoria',
        'descuento',
        'caracteristicas',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'descuento' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaServicio::class, 'id_categoria', 'id_categoria');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\ServicioHorario::class, 'servicio_id', 'id_servicio');
    }

    // ✅ Citas por pivot (tabla real: cita_servicio)
    public function citas()
    {
        return $this->belongsToMany(
            \App\Models\Cita::class,
            'cita_servicio',   // ✅ CORREGIDO (antes estaba 'citas_servicio')
            'id_servicio',
            'id_cita'
        )
        ->withTimestamps()
        ->withPivot([
            'id_empleado',
            'precio_snapshot',
            'duracion_snapshot',
            'hora_inicio',
            'hora_fin',
            'orden',
        ])
        ->withPivot([
            'id_empleado','precio_snapshot','duracion_snapshot','hora_inicio','hora_fin','orden',
        ])
        ->orderByPivot('orden');

    }

    public function empleados()
    {
        return $this->belongsToMany(
            \App\Models\Empleado::class,
            'servicio_empleado',
            'servicio_id',
            'empleado_id',
            'id_servicio',
            'id'
        )->withTimestamps();
    }
}
