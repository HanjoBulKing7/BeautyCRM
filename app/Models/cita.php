<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_cliente',
        'id_servicio',
        'id_empleado',
        'fecha_cita',
        'hora_cita',
        'estado_cita',
        'observaciones',
        'google_event_id',
        'synced_with_google',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'synced_with_google' => 'boolean',
    ];

    /**
     * Relación con el cliente (User)
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }

    /**
     * Relación con el servicio
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    /**
     * Relación con el empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}