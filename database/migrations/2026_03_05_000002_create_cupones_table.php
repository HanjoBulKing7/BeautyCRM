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
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Código único del cupón (ej: VERANO2026)
            $table->decimal('porcentaje_descuento', 5, 2); // Porcentaje de descuento (ej: 15.00)
            $table->text('descripcion')->nullable(); // Descripción del cupón
            $table->date('fecha_inicio'); // Fecha de inicio de validez
            $table->date('fecha_fin'); // Fecha de fin de validez
            $table->integer('uso_maximo')->nullable(); // Número máximo de usos (null = ilimitado)
            $table->integer('usos')->default(0); // Contador de usos
            $table->boolean('activo')->default(true); // Si el cupón está activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
