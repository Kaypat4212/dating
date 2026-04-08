<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('login_streak')->default(0)->after('credit_balance');
            $table->date('last_checkin_date')->nullable()->after('login_streak');
            $table->unsignedInteger('streak_freeze_count')->default(0)->after('last_checkin_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_streak', 'last_checkin_date', 'streak_freeze_count']);
        });
    }
};
