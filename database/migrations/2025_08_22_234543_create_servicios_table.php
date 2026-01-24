<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id('id_servicio');
            $table->string('nombre_servicio', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_minutos');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->unsignedBigInteger('id_categoria')->nullable();

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias_servicios')
                ->nullOnDelete();

            $table->string('imagen')->nullable();
            $table->decimal('descuento', 10, 2)->default(0);
            $table->text('caracteristicas')->nullable();
            $table->timestamps();

            $table->index('id_categoria');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};