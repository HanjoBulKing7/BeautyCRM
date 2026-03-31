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
        Schema::create('producto_venta_producto', function (Blueprint $table) {
            $table->id();
            
            // Relación con ventas_productos
            $table->foreignId('venta_producto_id')
                  ->constrained('ventas_productos')
                  ->cascadeOnDelete();
            
            // Creación explícita de la columna y la llave foránea apuntando a 'id'
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('productos')
                  ->cascadeOnDelete();
                  
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_venta_producto');
    }
};