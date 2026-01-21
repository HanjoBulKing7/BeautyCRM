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
        'categoria', // ← Cambiado de id_categoria a categoria (string)
        'descuento',          
        'caracteristicas'     
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'descuento' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con las citas
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_servicio');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\ServicioHorario::class, 'servicio_id', 'id_servicio');
    }


    public function servicios()
    {
    return $this->belongsToMany(
        Servicio::class,
        'cita_servicio',
        'id_cita',
        'id_servicio'
    )
    ->withTimestamps()
    ->withPivot(['precio_snapshot', 'duracion_snapshot']);
    }
}