<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    protected $keyType = 'int';
    protected $attributes = [
        'descuento' => 0.00,
    ];
    
    protected $fillable = [
        'nombre_servicio',
        'descripcion',
        'precio',
        'duracion_minutos',
        'estado',
        'imagen',
        'id_categoria',
        'caracteristicas',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaServicio::class, 'id_categoria', 'id_categoria');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\ServicioHorario::class, 'servicio_id', 'id_servicio');
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

    // ✅ Citas por pivot (tabla real: cita_servicio)
    public function citas()
    {
        return $this->belongsToMany(
            \App\Models\Cita::class,
            'cita_servicio',   // ✅ CORREGIDO (antes estaba 'citas_servicio')
            'id_servicio',
            'id_cita'
        )
        ->withTimestamps()
        ->withPivot([
            'id_empleado',
            'precio_snapshot',
            'duracion_snapshot',
            'hora_inicio',
            'hora_fin',
            'orden',
        ])
        ->withPivot([
            'id_empleado','precio_snapshot','duracion_snapshot','hora_inicio','hora_fin','orden',
        ])
        ->orderByPivot('orden');

    }

    public function empleados()
    {
        return $this->belongsToMany(
            \App\Models\Empleado::class,
            'servicio_empleado',
            'servicio_id',
            'empleado_id',
            'id_servicio',
            'id'
        )->withTimestamps();
    }
}
