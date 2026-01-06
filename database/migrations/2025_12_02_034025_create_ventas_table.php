<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->unsignedBigInteger('id_cita')->unique();
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('total', 10, 2);
            
            // Forma de pago SOLO aquí
            $table->enum('forma_pago', ['efectivo', 'tarjeta_credito', 'tarjeta_debito', 'transferencia', 'mixto']);
            
            $table->string('metodo_pago_especifico')->nullable();
            $table->string('referencia_pago')->nullable();
            $table->text('notas')->nullable();
            $table->decimal('comision_empleado', 10, 2)->nullable()->default(0);
            
            $table->foreign('id_cita')
                ->references('id_cita')
                ->on('citas')
                ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};