<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clientes') && !Schema::hasColumn('clientes', 'baja_correos_at')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->timestamp('baja_correos_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clientes', 'baja_correos_at')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('baja_correos_at');
            });
        }
    }
};
