<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioHorario extends Model
{
    protected $table = 'servicio_horarios';

    protected $fillable = [
        'servicio_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'id_servicio');
    }
}
