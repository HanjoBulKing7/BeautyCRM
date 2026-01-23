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

    // ✅ Servicio pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(CategoriaServicio::class, 'id_categoria', 'id_categoria');
    }

    // ✅ Horarios (si existe tabla servicio_horarios con FK servicio_id)
    public function horarios()
    {
        return $this->hasMany(\App\Models\ServicioHorario::class, 'servicio_id', 'id_servicio');
    }

    // ✅ Citas por pivot (cita_servicio = body)
    public function citas()
    {
        return $this->belongsToMany(
            \App\Models\Cita::class,
            'cita_servicio',
            'id_servicio',
            'id_cita'
        )
        ->withTimestamps()
        ->withPivot(['precio_snapshot', 'duracion_snapshot']);
    }
}
