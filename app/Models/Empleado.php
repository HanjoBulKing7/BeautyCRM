<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'puesto',
        'departamento',
        'fecha_contratacion',
        'estatus',
        'informacion_legal',
    ];

    protected $casts = [
        'fecha_contratacion' => 'date',
        'estatus' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'empleado_id');
    }
}
