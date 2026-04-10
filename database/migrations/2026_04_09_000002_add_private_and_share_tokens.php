<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Chat rooms: add private + invite_token ────────────────────────────
        if (Schema::hasTable('chat_rooms') && ! Schema::hasColumn('chat_rooms', 'is_private')) {
            Schema::table('chat_rooms', function (Blueprint $table) {
                $table->boolean('is_private')->default(false)->after('requires_approval');
                $table->string('invite_token', 32)->nullable()->unique()->after('is_private');
            });
        }

        // ── Forum topics: add share_token ─────────────────────────────────────
        if (Schema::hasTable('forum_topics') && ! Schema::hasColumn('forum_topics', 'share_token')) {
            Schema::table('forum_topics', function (Blueprint $table) {
                $table->string('share_token', 32)->nullable()->unique()->after('slug');
            });
        }

        // ── Forum categories: add share_token ─────────────────────────────────
        if (Schema::hasTable('forum_categories') && ! Schema::hasColumn('forum_categories', 'share_token')) {
            Schema::table('forum_categories', function (Blueprint $table) {
                $table->string('share_token', 32)->nullable()->unique()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chat_rooms', 'invite_token')) {
            Schema::table('chat_rooms', function (Blueprint $table) {
                $table->dropColumn(['is_private', 'invite_token']);
            });
        }
        if (Schema::hasColumn('forum_topics', 'share_token')) {
            Schema::table('forum_topics', function (Blueprint $table) {
                $table->dropColumn('share_token');
            });
        }
        if (Schema::hasColumn('forum_categories', 'share_token')) {
            Schema::table('forum_categories', function (Blueprint $table) {
                $table->dropColumn('share_token');
            });
        }
    }
};
