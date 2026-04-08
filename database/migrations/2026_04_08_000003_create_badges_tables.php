<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Badge definitions
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();           // e.g. streak_7, likes_100
            $table->string('name', 80);
            $table->string('emoji', 8)->default('🏅');
            $table->string('description', 200);
            $table->string('category', 40)->default('general'); // streak, social, profile, premium
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User ↔ badge pivot
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->useCurrent();
            $table->boolean('is_pinned')->default(false); // show on profile card
            $table->unique(['user_id', 'badge_id']);
        });

        // Seed default badge definitions
        DB::table('badges')->insert([
            ['key' => 'first_match',    'name' => 'First Match',       'emoji' => '💞', 'description' => 'Got your first match!',              'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'streak_3',       'name' => '3-Day Streak',      'emoji' => '🔥', 'description' => 'Logged in 3 days in a row.',         'category' => 'streak',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'streak_7',       'name' => '7-Day Streak',      'emoji' => '🌟', 'description' => 'Logged in 7 days in a row!',         'category' => 'streak',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'streak_30',      'name' => '30-Day Streak',     'emoji' => '🏆', 'description' => 'Logged in 30 days in a row!',        'category' => 'streak',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'streak_100',     'name' => '100-Day Streak',    'emoji' => '👑', 'description' => 'Logged in 100 days in a row!',       'category' => 'streak',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'likes_10',       'name' => 'Rising Star',       'emoji' => '⭐', 'description' => 'Received 10 profile likes.',         'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'likes_50',       'name' => 'Popular',           'emoji' => '💫', 'description' => 'Received 50 profile likes.',         'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'likes_100',      'name' => 'Fan Favourite',     'emoji' => '❤️',  'description' => 'Received 100 profile likes!',        'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'matches_5',      'name' => 'Connector',         'emoji' => '🤝', 'description' => 'Made 5 matches.',                   'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'matches_25',     'name' => 'Super Connector',   'emoji' => '💥', 'description' => 'Made 25 matches.',                  'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'profile_100',    'name' => 'Complete Profile',  'emoji' => '✅', 'description' => 'Filled out your entire profile.',   'category' => 'profile',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'first_message',  'name' => 'Conversationalist', 'emoji' => '💬', 'description' => 'Sent your first message.',          'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'verified',       'name' => 'Verified',          'emoji' => '✔️',  'description' => 'Verified account.',                 'category' => 'profile',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'premium',        'name' => 'Premium Member',    'emoji' => '💎', 'description' => 'Active Premium subscriber.',        'category' => 'premium',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'feed_post_1',    'name' => 'First Post',        'emoji' => '📸', 'description' => 'Published your first feed post.',   'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'feed_likes_10',  'name' => 'Trending',          'emoji' => '🚀', 'description' => 'Received 10 likes on a feed post.', 'category' => 'social',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
