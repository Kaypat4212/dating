<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            // Email notification switches — all default to true (opt-in)
            $table->boolean('email_new_message')->default(true)->after('show_online_only');
            $table->boolean('email_new_match')->default(true)->after('email_new_message');
            $table->boolean('email_profile_liked')->default(true)->after('email_new_match');
            $table->boolean('email_wave_received')->default(true)->after('email_profile_liked');
            $table->boolean('email_travel_interest')->default(true)->after('email_wave_received');
            $table->boolean('email_login_alert')->default(true)->after('email_travel_interest');
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'email_new_message',
                'email_new_match',
                'email_profile_liked',
                'email_wave_received',
                'email_travel_interest',
                'email_login_alert',
            ]);
        });
    }
};
