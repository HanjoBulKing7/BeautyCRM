<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Código único, ej: PROMO2024, CUMPLEAÑOS, etc
            $table->string('nombre'); // Nombre descriptivo, ej: "Descuento Cumpleaños", "Black Friday"
            $table->text('descripcion')->nullable();
            
            $table->enum('tipo_descuento', ['porcentaje', 'monto'])->default('porcentaje');
            $table->decimal('valor_descuento', 10, 2); // % o cantidad en MXN
            
            $table->decimal('descuento_maximo', 10, 2)->nullable(); // Límite máximo del descuento en MXN
            $table->decimal('monto_minimo', 10, 2)->nullable(); // Monto mínimo para aplicar el cupón
            
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            
            $table->integer('cantidad_usos')->nullable(); // Máximo de usos totales (null = ilimitado)
            $table->integer('usos_actuales')->default(0);
            
            $table->integer('cantidad_por_cliente')->default(1); // Cuántas veces puede usar el mismo cliente
            
            $table->boolean('aplica_cumpleaños')->default(false); // Flag para saber si es descuento de cumpleaños
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
