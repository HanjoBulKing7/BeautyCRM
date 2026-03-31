<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVentaProducto extends Model
{
    protected $table = 'producto_venta_producto';

    protected $fillable = [
        'venta_producto_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id');
    }

    public function venta()
    {
        return $this->belongsTo(\App\Models\VentasProducto::class, 'venta_producto_id');
    }
}
