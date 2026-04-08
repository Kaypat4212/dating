<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Premium: when true, sender can see blue double-ticks (read receipts)
            // When false, read_at is still stamped in DB but sender never gets notified
            $table->boolean('read_receipts_enabled')->default(true)->after('hide_last_seen');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('read_receipts_enabled');
        });
    }
};
