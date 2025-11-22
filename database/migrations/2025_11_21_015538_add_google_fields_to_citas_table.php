<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->after('id_cita');
            $table->boolean('synced_with_google')->default(false)->after('google_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'synced_with_google']);
        });
    }
};