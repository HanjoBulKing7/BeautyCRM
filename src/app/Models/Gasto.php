<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $fillable = [
        'sucursal_id', 'usuario_id', 'ruta_id', 'fecha', 'categoria',
        'descripcion', 'monto', 'metodo_pago', 'comprobante_url'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function sucursal(): BelongsTo 
    { 
        return $this->belongsTo(Sucursal::class); 
    }
    
    public function usuario(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'usuario_id'); 
    }
    
    public function ruta(): BelongsTo 
    { 
        return $this->belongsTo(Ruta::class); 
    }
}