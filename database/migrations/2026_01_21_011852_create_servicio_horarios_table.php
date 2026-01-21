<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicio_horarios', function (Blueprint $table) {
            $table->id();

            // OJO: tu PK en servicios es id_servicio (no id)
            $table->unsignedBigInteger('servicio_id');

            // 0=Domingo ... 6=Sábado (estándar)
            $table->tinyInteger('dia_semana');

            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->timestamps();

            $table->foreign('servicio_id')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');

            $table->index(['servicio_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_horarios');
    }
};
