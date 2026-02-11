<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioEmpleado extends Model
{
    use HasFactory;

    protected $table = 'servicio_empleado';

    protected $fillable = [
        'empleado_id',
        'servicio_id',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'id_servicio');
    }
}
