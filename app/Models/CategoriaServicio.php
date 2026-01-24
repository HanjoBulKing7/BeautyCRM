<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaServicio extends Model
{
    use HasFactory;

    protected $table = 'categorias_servicios';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getRouteKeyName()
    {
        return 'id_categoria';
    }

    public function productos(): HasMany
    {
        return $this->hasMany(\App\Models\Producto::class, 'id_categoria', 'id_categoria');
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(\App\Models\Servicio::class, 'id_categoria', 'id_categoria');
    }
}
