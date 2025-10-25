<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        return $this->belongsTo(\App\Models\Sucursal::class, 'sucursal_id');
    }

    // ================================
    // ⚙️ SCOPES OPCIONALES
    // ================================

    /**
     * Filtrar empleados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Filtrar empleados por roles (vendedor o gerente)
     */
    public function scopeEmpleados($query)
    {
        return $query->whereIn('rol', ['vendedor', 'gerente']);
    }
}
