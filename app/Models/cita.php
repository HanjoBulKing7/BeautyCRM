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
        'last_sync_at',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'synced_with_google' => 'boolean',
        'last_sync_at' => 'datetime',
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
        return $this->belongsTo(User::class, 'id_empleado', 'id');
    }

    /**
     * Obtener la fecha y hora completa de inicio
     */
    public function getStartDateTimeAttribute()
    {
        return $this->fecha_cita->format('Y-m-d') . 'T' . $this->hora_cita . ':00';
    }

    /**
     * Calcular la fecha y hora de fin (asumiendo duración del servicio)
     */
    public function getEndDateTimeAttribute()
    {
        $start = \Carbon\Carbon::parse($this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita);
        $duration = $this->servicio ? $this->servicio->duracion : 60; // duración en minutos, default 60
        return $start->addMinutes($duration)->format('Y-m-d\TH:i:s');
    }

    /**
     * Verificar si está sincronizada con Google Calendar
     */
    public function isSyncedWithGoogle()
    {
        return $this->synced_with_google && !empty($this->google_event_id);
    }
}