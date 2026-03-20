<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tracks the current premium tier (30day / 90day / 365day)
            $table->string('premium_plan', 20)->nullable()->after('premium_expires_at');
            // Premium-only: hide last_active_at from other users (365day plan only)
            $table->boolean('hide_last_seen')->default(false)->after('premium_plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['premium_plan', 'hide_last_seen']);
        });
    }
};
