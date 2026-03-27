<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_music_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('service', ['spotify', 'apple_music'])->default('spotify');
            $table->string('service_user_id')->nullable();
            $table->string('display_name')->nullable();
            $table->string('profile_url')->nullable();
            $table->string('avatar_url')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('top_artists')->nullable();
            $table->json('top_tracks')->nullable();
            $table->json('top_genres')->nullable();
            $table->string('anthem_track_id')->nullable();
            $table->string('anthem_track_name')->nullable();
            $table->string('anthem_artist_name')->nullable();
            $table->string('anthem_preview_url')->nullable();
            $table->string('anthem_album_art')->nullable();
            $table->boolean('show_on_profile')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_music_profiles');
    }
};
