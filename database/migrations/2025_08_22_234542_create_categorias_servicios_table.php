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
        Schema::create('categorias_servicios', function (Blueprint $table) {
            $table->bigIncrements('id_categoria');

            $table->string('nombre', 120);
            $table->string('imagen', 255)->nullable();

            // puedes manejarlo como enum o string; dejo string para flexibilidad
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();

            // Opcional: indexes extra
            $table->index('estado');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_servicios');
    }
};
