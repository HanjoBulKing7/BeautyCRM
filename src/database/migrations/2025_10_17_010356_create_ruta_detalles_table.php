<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained('rutas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('carga_inicial')->default(0);
            $table->integer('recargas')->default(0);
            $table->integer('devoluciones')->default(0);
            $table->integer('ventas')->default(0);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_detalles');
    }
};
