<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Availability status + vibe badge + dealbreaker prefs
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('availability_status', 40)->nullable()->after('mood_status');
            // availability options: 'free_tonight','busy_this_week','looking_irl','open_to_chat','offline'
            $table->string('vibe_badge', 40)->nullable()->after('availability_status');
            // vibe options: 'adventurer','homebody','social_butterfly','intellectual','romantic','wild_card'
        });

        // Dealbreaker preferences (stored as JSON on profiles)
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('dealbreakers')->nullable()->after('vibe_badge');
            // keys: no_smokers, no_drinkers, must_want_kids, no_kids, max_distance_km, min_age, max_age
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['availability_status', 'vibe_badge', 'dealbreakers']);
        });
    }
};
