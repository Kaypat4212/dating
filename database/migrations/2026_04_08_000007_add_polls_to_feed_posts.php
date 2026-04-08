<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add poll columns to feed_posts
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->enum('post_type', ['post', 'poll'])->default('post')->after('is_active');
            $table->string('poll_question')->nullable()->after('post_type');
            $table->json('poll_options')->nullable()->after('poll_question');
        });

        // Poll votes
        Schema::create('feed_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('option_index');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['post_id', 'user_id']); // one vote per user per poll
            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_poll_votes');
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->dropColumn(['post_type', 'poll_question', 'poll_options']);
        });
    }
};
