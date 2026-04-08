<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (! Schema::hasColumn('user_preferences', 'email_weekly_digest')) {
                $table->boolean('email_weekly_digest')->default(true)->after('email_login_alert');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (Schema::hasColumn('user_preferences', 'email_weekly_digest')) {
                $table->dropColumn('email_weekly_digest');
            }
        });
    }
};
