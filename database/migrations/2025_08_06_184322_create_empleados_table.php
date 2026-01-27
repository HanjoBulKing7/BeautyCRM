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
            $table->id();

            // ✅ Relación con users (login)
            $table->unsignedBigInteger('user_id')->unique(); // 1 a 1
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Perfil empleado
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 20);
            $table->text('informacion_legal')->nullable();

            $table->string('puesto')->nullable();
            $table->string('departamento')->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->enum('estatus', ['activo', 'inactivo', 'vacaciones'])->default('activo');

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