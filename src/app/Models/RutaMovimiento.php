<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaMovimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruta_detalle_id',
        'fecha',
        'ventas',
        'recargas',
        'devoluciones',
        'total'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function rutaDetalle()
    {
        return $this->belongsTo(RutaDetalle::class);
    }
}