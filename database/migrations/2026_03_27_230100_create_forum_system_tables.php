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
            $table->string('color')->default('#6366f1');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_verified')->default(false); // Only verified users can post
            $table->timestamps();
        });

        // Forum Topics/Threads
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->json('tags')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_answered')->default(false); // For Q&A style forums
            $table->boolean('is_flagged')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->foreignId('last_reply_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'is_pinned', 'last_reply_at']);
            $table->index('is_answered');
            $table->fullText(['title', 'content']);
        });

        // Forum Replies
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->cascadeOnDelete();
            $table->longText('content');
            $table->boolean('is_best_answer')->default(false);
            $table->integer('likes_count')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
            
            $table->index(['topic_id', 'created_at']);
            $table->index('is_best_answer');
        });

        // Forum Reply Likes
        Schema::create('forum_reply_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reply_id')->constrained('forum_replies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['reply_id', 'user_id']);
        });

        // Forum Topic Likes
        Schema::create('forum_topic_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['topic_id', 'user_id']);
        });

        // Forum Topic Followers (for notifications)
        Schema::create('forum_topic_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['topic_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topic_followers');
        Schema::dropIfExists('forum_topic_likes');
        Schema::dropIfExists('forum_reply_likes');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_categories');
    }
};
