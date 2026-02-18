<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_cita';

    private const TZ = 'America/Mexico_City';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_CONFIRMADA = 'confirmada';
    public const ESTADO_CANCELADA = 'cancelada';
    public const ESTADO_COMPLETADA = 'completada';

    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'fecha_cita',
        'hora_cita',
        'descuento',
        'estado_cita',
        'metodo_pago',
        'observaciones',
        'duracion_total_minutos',
        'google_event_id',
        'synced_with_google',
        'last_sync_at',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'synced_with_google' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // ✅ Multi-servicio (pivot real: cita_servicio)
    public function servicios()
    {
        return $this->belongsToMany(
            Servicio::class,
            'cita_servicio',   // ✅ tabla real
            'id_cita',
            'id_servicio'
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
        ->orderBy('pivot_orden'); // respeta orden si lo usas en UI
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_cita', 'id_cita');
    }

    public function getStartDateTimeAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) return null;

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;
        return Carbon::parse($raw, self::TZ)->toIso8601String();
    }

    public function getEndDateTimeAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) return null;

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;
        $start = Carbon::parse($raw, self::TZ);

        $duracion = (int) ($this->duracion_total_minutos ?? 0);
        if ($duracion <= 0 && $this->relationLoaded('servicios')) {
            $duracion = (int) $this->servicios->sum(fn($s) => (int)($s->pivot->duracion_snapshot ?? 0));
        }
        if ($duracion <= 0) $duracion = 60;

        return $start->copy()->addMinutes($duracion)->toIso8601String();
    }

    public function getFechaHoraAttribute()
    {
        if (!$this->fecha_cita || !$this->hora_cita) return null;

        $raw = $this->fecha_cita->format('Y-m-d') . ' ' . $this->hora_cita;
        return Carbon::parse($raw, self::TZ);
    }
}
