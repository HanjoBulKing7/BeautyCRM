<?php

namespace App\Observers;

use App\Models\Cita;
use App\Models\Venta;

class CitaObserver
{
    /**
     * Handle the Cita "updated" event.
     */
    public function updated(Cita $cita): void
    {
        // Verificar si la cita acaba de ser marcada como completada
        if ($cita->isDirty('estado_cita') && $cita->estado_cita === 'completada') {
            
            // Verificar que no exista ya una venta para esta cita
            if (!$cita->venta()->exists()) {
                
                // Obtener datos necesarios
                $servicio = $cita->servicio;
                $total = $servicio->precio ?? 0;
                
                // En un caso real, aquí obtendrías la forma de pago de un modal o formulario
                // Por ahora, usamos un valor por defecto o lo pasamos en la request
                $forma_pago = request()->input('forma_pago', 'efectivo');
                
                // Calcular comisión (ejemplo: 20% del servicio)
                $comision_empleado = $total * 0.20; // Ajusta esta fórmula según tu negocio
                
                // Crear la venta automáticamente
                Venta::create([
                    'id_cita' => $cita->id_cita,
                    'total' => $total,
                    'forma_pago' => $forma_pago,
                    'comision_empleado' => $comision_empleado,
                    'notas' => 'Venta generada automáticamente al completar la cita'
                ]);
            }
        }
        
        // Si una cita se cancela y tiene venta, eliminar la venta
        if ($cita->isDirty('estado_cita') && $cita->estado_cita === 'cancelada') {
            if ($cita->venta) {
                $cita->venta->delete();
            }
        }
    }
}