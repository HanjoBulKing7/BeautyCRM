<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita_servicio', function (Blueprint $table) {
            $table->id();

            // Usamos tus llaves actuales: id_cita y id_servicio (compatibles con tu schema)
            $table->unsignedBigInteger('id_cita');
            $table->unsignedBigInteger('id_servicio');

            // Snapshots opcionales (recomendados para que no cambie el histórico si editas el servicio después)
            $table->decimal('precio_snapshot', 10, 2)->nullable();
            $table->unsignedInteger('duracion_snapshot')->nullable(); // minutos

            $table->timestamps();

            // Evita duplicar el mismo servicio en la misma cita
            $table->unique(['id_cita', 'id_servicio']);

            $table->foreign('id_cita')
                ->references('id_cita')
                ->on('citas')
                ->onDelete('cascade');

            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita_servicio');
    }
};
