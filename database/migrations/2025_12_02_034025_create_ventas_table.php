<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->unsignedBigInteger('id_cita')->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_servicio');
            
            $table->datetime('fecha_venta')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            
            $table->enum('forma_pago', [
                'efectivo', 
                'tarjeta_credito', 
                'tarjeta_debito', 
                'transferencia', 
                'mixto'
            ])->default('efectivo');
            
            $table->enum('estado_venta', [
                'pendiente', 
                'pagada', 
                'cancelada'
            ])->default('pagada');
            
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_cita')
                  ->references('id_cita')
                  ->on('citas')
                  ->onDelete('set null');

            $table->foreign('id_cliente')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('id_empleado')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('id_servicio')
                  ->references('id_servicio')
                  ->on('servicios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};