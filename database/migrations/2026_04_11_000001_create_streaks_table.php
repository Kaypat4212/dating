<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->integer('count')->default(0);
            $table->date('last_interaction_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure unique streaks between two users (bidirectional)
            $table->unique(['user1_id', 'user2_id']);
            $table->index('last_interaction_date');
        });

        Schema::create('disappearing_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->string('media_path');
            $table->string('media_type'); // image, video, audio
            $table->boolean('viewed')->default(false);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_id', 'viewed']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disappearing_content');
        Schema::dropIfExists('streaks');
    }
};
