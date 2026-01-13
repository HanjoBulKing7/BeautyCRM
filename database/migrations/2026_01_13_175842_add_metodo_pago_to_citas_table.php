<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('id_servicio') // ajusta si quieres
                ->constrained('categorias_servicios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};
