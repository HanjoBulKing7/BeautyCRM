<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CategoriaServicio extends Model
{
    use HasFactory;

    protected $table = 'categorias_servicios';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
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

    public function getImagenUrlAttribute(): ?string
    {
        $imagen = (string) ($this->imagen ?? '');
        if ($imagen === '') {
            return null;
        }

        if (Str::startsWith($imagen, ['http://', 'https://'])) {
            return $imagen;
        }

        if (Str::startsWith($imagen, ['images/', '/images/'])) {
            return asset(ltrim($imagen, '/'));
        }

        $path = ltrim($imagen, '/');
        if (Str::startsWith($path, 'storage/')) {
            $path = substr($path, 8);
        }

        $publicStoragePath = public_path('storage');
        if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
            return asset('storage/' . $path);
        }

        return route('media.public', ['path' => $path]);
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
