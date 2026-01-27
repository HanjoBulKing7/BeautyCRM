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
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');

            // ✅ Ahora citamos tablas “perfil”
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('empleado_id')->nullable();

            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->enum('estado_cita', ['pendiente', 'confirmada', 'cancelada', 'completada'])->default('pendiente');
            $table->text('observaciones')->nullable();

            // Google Calendar
            $table->string('google_event_id')->nullable();
            $table->boolean('synced_with_google')->default(false);
            $table->timestamp('last_sync_at')->nullable();

            $table->foreign('cliente_id')
                ->references('id')->on('clientes')
                ->onDelete('cascade');

            $table->foreign('empleado_id')
                ->references('id')->on('empleados')
                ->onDelete('set null');

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};