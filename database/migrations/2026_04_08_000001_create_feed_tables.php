<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Feed posts ──────────────────────────────────────────────────────
        Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body')->nullable();
            // Media (optional)
            $table->string('media_path')->nullable();
            $table->enum('media_type', ['image', 'video'])->nullable();
            // Repost of another post
            $table->foreignId('original_post_id')->nullable()->constrained('feed_posts')->nullOnDelete();
            // Cached counters
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('reposts_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['is_active', 'created_at']);
        });

        // ── Post likes ──────────────────────────────────────────────────────
        Schema::create('feed_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['post_id', 'user_id']);
        });

        // ── Post comments ───────────────────────────────────────────────────
        Schema::create('feed_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('feed_post_comments')->cascadeOnDelete();
            $table->text('body');
            $table->unsignedInteger('likes_count')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
        });

        // ── Comment likes ───────────────────────────────────────────────────
        Schema::create('feed_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('feed_post_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_comment_likes');
        Schema::dropIfExists('feed_post_comments');
        Schema::dropIfExists('feed_post_likes');
        Schema::dropIfExists('feed_posts');
    }
};
