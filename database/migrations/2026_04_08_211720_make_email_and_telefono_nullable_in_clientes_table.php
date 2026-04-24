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
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('telefono')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
        });
    }
};
