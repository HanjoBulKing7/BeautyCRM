<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Producción ya tiene la tabla creada a mano; solo se crea donde falte.
        if (Schema::hasTable('cupones')) {
            return;
        }

        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo_descuento', ['porcentaje', 'monto'])->default('porcentaje');
            $table->decimal('valor_descuento', 10, 2);
            $table->decimal('descuento_maximo', 10, 2)->nullable();
            $table->decimal('monto_minimo', 10, 2)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedInteger('cantidad_usos')->nullable();
            $table->unsignedInteger('usos_actuales')->default(0);
            $table->unsignedInteger('cantidad_por_cliente')->default(1);
            $table->boolean('aplica_cumpleaños')->default(false);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // No se elimina: la tabla puede ser anterior a esta migración.
    }
};
