<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nombre',
        'descripcion',
        'precio',
        'precio_proveedor', // ✅ Nuevo campo agregado
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_proveedor' => 'decimal:2', // ✅ También lo casteamos como decimal
        'activo' => 'boolean',
    ];

    // ================================
    // 🔗 RELACIONES
    // ================================

    /**
     * Relación con existencias (stock por sucursal)
     */
    public function existencias()
    {
        return $this->hasMany(Existencia::class);
    }

    /**
     * Relación con movimientos de inventario
     */
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class);
    }
}
