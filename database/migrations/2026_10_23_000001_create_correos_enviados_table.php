<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('correos_enviados')) {
            return;
        }

        Schema::create('correos_enviados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->index();
            $table->string('tipo', 40);
            // Año para cumpleaños, id_cita para agradecimiento: evita enviar dos veces lo mismo.
            $table->string('referencia', 40);
            $table->string('email');
            $table->timestamp('enviado_at');
            $table->timestamps();

            $table->unique(['cliente_id', 'tipo', 'referencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correos_enviados');
    }
};
