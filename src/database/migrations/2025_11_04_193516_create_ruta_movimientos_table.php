<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRutaMovimientosTable extends Migration
{
    public function up()
    {
        Schema::create('ruta_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_detalle_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->integer('ventas')->default(0);
            $table->integer('recargas')->default(0);
            $table->integer('devoluciones')->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['ruta_detalle_id', 'fecha']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ruta_movimientos');
    }
}