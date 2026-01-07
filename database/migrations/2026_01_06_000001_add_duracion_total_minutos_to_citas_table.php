
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Duración final de la cita (en minutos). Puede ser calculada o ajustada manualmente.
            $table->unsignedInteger('duracion_total_minutos')->nullable()->after('hora_cita');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn('duracion_total_minutos');
        });
    }
};
