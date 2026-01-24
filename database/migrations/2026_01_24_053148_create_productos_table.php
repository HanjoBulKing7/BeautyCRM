<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 120);
            $table->decimal('precio', 10, 2);
            $table->text('descripcion')->nullable();

            // FK hacia categorias_servicios.id_categoria
            $table->unsignedBigInteger('id_categoria');

            // Estado del producto
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();

            $table->index('id_categoria');

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias_servicios')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // Opcional: evita nombres repetidos dentro de la misma categoría
            $table->unique(['id_categoria', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
