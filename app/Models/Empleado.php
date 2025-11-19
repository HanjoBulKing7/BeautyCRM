<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido', 
        'telefono',
        'informacion_legal',
        'puesto',
        'departamento',
        'fecha_contratacion',
        'estatus',
    ];

    protected $casts = [
        'fecha_contratacion' => 'date',
        'estatus' => 'string'
    ];
}