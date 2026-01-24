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
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); // id (bigint unsigned)

            $table->string('nombre', 120);

            // Precio: exacto para dinero (ej: 999999.99)
            $table->decimal('precio', 10, 2);

            // Guarda ruta tipo: productos/archivo.webp
            $table->string('imagen')->nullable();

            // FK hacia categorias_servicios.id_categoria
            $table->unsignedBigInteger('id_categoria');

            $table->timestamps();

            $table->index('id_categoria');

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias_servicios')
                ->onUpdate('cascade')
                ->onDelete('cascade'); // o ->nullOnDelete() si prefieres permitir borrar categoría
            $table->unique(['id_categoria', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
