<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_cita';

    // ✅ Zona horaria real del negocio
    private const TZ = 'America/Mexico_City';

    protected $fillable = [
        'id_cliente',
        'id_servicio',
        'id_empleado',
        'fecha_cita',
        'hora_cita',
        'descuento',
        'estado_cita',
        'metodo_pago',
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

    /**
     * ✅ Accesor: inicio en ISO8601 pero interpretado desde el inicio en TZ México
     * Ej: 2025-12-23T20:00:00-06:00
     */
    public function getStartDateTimeAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) {
            return null;
        }

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;

        // ✅ Interpretar como hora local de México (NO convertir desde UTC)
        $dt = Carbon::parse($raw, self::TZ);

        return $dt->toIso8601String();
    }

    /**
     * ✅ Accesor: fin = inicio + duración del servicio (min)
     */
    public function getEndDateTimeAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) {
            return null;
        }

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;

        $start = Carbon::parse($raw, self::TZ);

        $duracion = (int) ($this->servicio->duracion ?? 60);

        $end = (clone $start)->addMinutes($duracion);

        return $end->toIso8601String();
    }

    /**
     * ✅ Accesor interno: objeto Carbon en TZ México
     */
    public function getFechaHoraAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) {
            return null;
        }

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;

        return Carbon::parse($raw, self::TZ);
    }

    public function servicios()
    {
    return $this->belongsToMany(
        Servicio::class,
        'cita_servicio',
        'id_cita',
        'id_servicio'
    )
    ->withTimestamps()
    ->withPivot(['precio_snapshot', 'duracion_snapshot']);
    }
}