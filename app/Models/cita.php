<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

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
        'last_sync_at'
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'synced_with_google' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'id_empleado');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_cita', 'id_cita');
    }

    // ✅ NUEVO: Accesor para startDateTime en formato ISO 8601
    public function getStartDateTimeAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) {
            return null;
        }
        
        // Combinar fecha y hora
        $dateTime = Carbon::parse($this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita);
        
        // Convertir a formato ISO 8601 con timezone
        return $dateTime->setTimezone('America/Mexico_City')->format('c'); // 'c' = ISO 8601
    }

    // ✅ NUEVO: Accesor para endDateTime (inicio + duración del servicio)
    public function getEndDateTimeAttribute()
    {
        if (!$this->startDateTime) {
            return null;
        }
        
        $start = Carbon::parse($this->startDateTime);
        
        // Obtener duración del servicio (en minutos)
        $duracion = 60; // valor por defecto 1 hora
        
        if ($this->servicio && $this->servicio->duracion) {
            $duracion = $this->servicio->duracion;
        }
        
        // Sumar duración al inicio
        $end = $start->copy()->addMinutes($duracion);
        
        return $end->format('c'); // 'c' = ISO 8601
    }

    // ✅ NUEVO: Accesor para fecha y hora combinadas (opcional, para uso interno)
    public function getFechaHoraAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) {
            return null;
        }
        
        return Carbon::parse($this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita);
    }
}