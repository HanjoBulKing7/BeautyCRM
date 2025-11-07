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
    const CATEGORIAS = [
        'servicios' => 'Servicios',
        'renta' => 'Renta',
        'insumos' => 'Insumos',
        'nomina' => 'Nómina',
        'mantenimiento' => 'Mantenimiento',
        'combustible' => 'Combustible',
        'alimentos' => 'Alimentos',
        'otros' => 'Otros'
    ];

    const METODOS_PAGO = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta' => 'Tarjeta'
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