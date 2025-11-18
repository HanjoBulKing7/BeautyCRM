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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id()->nullable;
            
            // Datos personales y de contacto
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 20);
            $table->text('informacion_legal')->nullable();
            
            // Información laboral
            $table->string('puesto')->nullable();
            $table->string('departamento')->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->enum('estatus', ['activo', 'inactivo', 'vacaciones'])->default('activo');
            
            // Relación 1:1 con users
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};