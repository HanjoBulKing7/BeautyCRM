<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha',
        'empleado_id',
        'total_venta',
    ];

    protected $casts = [
        'fecha' => 'date', // 👈 Esto convierte automáticamente el string en Carbon
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function detalles()
    {
        return $this->hasMany(RutaDetalle::class);
    }
}
