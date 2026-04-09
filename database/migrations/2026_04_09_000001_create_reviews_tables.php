<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Reviews ──────────────────────────────────────────────────────────
        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                // Nullable: guests can leave reviews without an account
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                // Guest-submitted fields (only used when user_id is null)
                $table->string('guest_name', 100)->nullable();
                $table->string('guest_email', 180)->nullable();
                $table->tinyInteger('rating')->unsigned(); // 1–5 stars
                $table->string('title', 160)->nullable();
                $table->text('body');
                // Admin moderation
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('admin_note')->nullable();
                $table->integer('helpful_count')->default(0);
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index('user_id');
            });
        }

        // ── Review Helpful Votes (one per user per review) ────────────────────
        if (! Schema::hasTable('review_helpful_votes')) {
            Schema::create('review_helpful_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['review_id', 'user_id']);
            });
        }

        // ── Review Comments (auth required) ───────────────────────────────────
        if (! Schema::hasTable('review_comments')) {
            Schema::create('review_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('review_comments')->cascadeOnDelete();
                $table->text('body');
                $table->boolean('is_approved')->default(true);
                $table->boolean('is_flagged')->default(false);
                $table->timestamps();

                $table->index(['review_id', 'created_at']);
                $table->index('parent_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
        Schema::dropIfExists('review_helpful_votes');
        Schema::dropIfExists('reviews');
    }
};
