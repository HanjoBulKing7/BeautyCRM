<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentasProducto extends Model
{
    protected $fillable = [
        'cliente_id',
        'user_id',
        'total',
        'metodo_pago',
    ];

    public function productosVendidos()
    {
        return $this->hasMany(\App\Models\ProductoVentaProducto::class, 'venta_producto_id');
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
