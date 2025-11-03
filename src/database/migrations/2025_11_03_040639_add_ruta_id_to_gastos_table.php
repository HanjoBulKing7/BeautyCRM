<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            // Agregamos la relación con rutas (opcional)
            $table->foreignId('ruta_id')
                ->nullable()
                ->constrained('rutas')
                ->cascadeOnUpdate()
                ->nullOnDelete()
                ->after('usuario_id'); // opcional, solo para orden
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign(['ruta_id']);
            $table->dropColumn('ruta_id');
        });
    }
};
