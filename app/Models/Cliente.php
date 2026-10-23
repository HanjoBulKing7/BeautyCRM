<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'fecha_nacimiento_registrada_at',
        'baja_correos_at',
    ];

    protected $casts = [
        'fecha_nacimiento'               => 'date',
        'fecha_nacimiento_registrada_at' => 'datetime',
        'baja_correos_at'                => 'datetime',
    ];

    public function aceptaPromociones(): bool
    {
        return $this->baja_correos_at === null && !empty($this->email);
    }

    // Usar en todo envío masivo de promociones.
    public function scopeAceptanPromociones($query)
    {
        return $query->whereNull('baja_correos_at')->whereNotNull('email');
    }

    public function urlBajaCorreos(): string
    {
        return \Illuminate\Support\Facades\URL::signedRoute('correos.baja', ['cliente' => $this->id]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'cliente_id');
    }
}
