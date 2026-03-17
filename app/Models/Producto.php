<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'id_categoria',
        'estado',
        'imagen', 
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaServicio::class, 'id_categoria', 'id_categoria');
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
}
