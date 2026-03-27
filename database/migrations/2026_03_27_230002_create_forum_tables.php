<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Forum Categories
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable(); // Hex color for UI
            $table->boolean('is_private')->default(false); // Premium only
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Forum Topics
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Topic creator
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_hidden')->default(false); // Moderation
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->foreignId('last_reply_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_reply_at')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'is_pinned', 'last_reply_at']);
            $table->index('is_hidden');
        });

        // Forum Replies
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->cascadeOnDelete(); // For threaded replies
            $table->text('content');
            $table->boolean('is_solution')->default(false); // Mark as accepted answer
            $table->boolean('is_hidden')->default(false); // Moderation
            $table->integer('likes_count')->default(0);
            $table->json('attachments')->nullable(); // Image URLs
            $table->timestamps();
            
            $table->index(['topic_id', 'created_at']);
        });

        // Forum Likes
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // forum_topics or forum_replies
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });

        // Forum Subscriptions (get notified of new replies)
        Schema::create('forum_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'topic_id']);
        });

        // Forum Moderators
        Schema::create('forum_moderators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('forum_categories')->cascadeOnDelete(); // null = global mod
            $table->timestamps();
            
            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_moderators');
        Schema::dropIfExists('forum_subscriptions');
        Schema::dropIfExists('forum_likes');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_categories');
    }
};
