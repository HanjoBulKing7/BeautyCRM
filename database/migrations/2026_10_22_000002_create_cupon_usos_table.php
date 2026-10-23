<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cupon_usos')) {
            Schema::create('cupon_usos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cupon_id')->index();
                $table->unsignedBigInteger('cliente_id')->index();
                $table->unsignedBigInteger('id_cita')->nullable()->index();
                $table->decimal('descuento', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('clientes') && !Schema::hasColumn('clientes', 'fecha_nacimiento_registrada_at')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->timestamp('fecha_nacimiento_registrada_at')->nullable()->after('fecha_nacimiento');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cupon_usos');

        if (Schema::hasColumn('clientes', 'fecha_nacimiento_registrada_at')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('fecha_nacimiento_registrada_at');
            });
        }
    }
};
