<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cita_servicio', function (Blueprint $table) {
            $table->foreignId('id_empleado')
                ->nullable()
                ->after('id_servicio')
                ->constrained('empleados')   // empleados.id
                ->nullOnDelete();

            // Para poder marcar ocupado por tramo
            $table->time('hora_inicio')->nullable()->after('duracion_snapshot');
            $table->time('hora_fin')->nullable()->after('hora_inicio');

            // Para mantener el orden de ejecución (por si luego haces timeline)
            $table->unsignedInteger('orden')->default(0)->after('hora_fin');

            $table->index(['id_empleado', 'hora_inicio', 'hora_fin']);
        });
    }

    public function down(): void
    {
        Schema::table('cita_servicio', function (Blueprint $table) {
            $table->dropIndex(['id_empleado', 'hora_inicio', 'hora_fin']);
            $table->dropForeign(['id_empleado']);
            $table->dropColumn(['id_empleado', 'hora_inicio', 'hora_fin', 'orden']);
        });
    }
};
