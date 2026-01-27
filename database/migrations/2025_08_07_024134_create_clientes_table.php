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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            // ✅ Relación con users (login)
            $table->unsignedBigInteger('user_id')->unique(); // 1 a 1
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Datos extra (perfil)
            $table->string('nombre');             // si quieres duplicar/override del name
            $table->string('email')->unique();    // si quieres guardar email aquí también
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
