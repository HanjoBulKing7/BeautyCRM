<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
        'sucursal_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ================================
    // 🔗 RELACIONES
    // ================================

    /**
     * Cada usuario pertenece a una sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Alias para usar “empleados” en las vistas
     */
    public function scopeEmpleados($query)
    {
        return $query->whereIn('rol', ['vendedor', 'gerente']);
    }
}
