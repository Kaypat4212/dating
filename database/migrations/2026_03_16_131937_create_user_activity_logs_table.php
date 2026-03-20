<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');              // login, message_sent, like_sent, report_sent, etc.
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('meta')->nullable();      // extra context (target_user_id, message_count, etc.)
            $table->string('flag')->nullable();    // null | 'suspicious' | 'spam'
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('flag');
        });

        // Add spam_score and last_flagged_at to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('spam_score')->default(0)->after('is_banned');
            $table->timestamp('last_flagged_at')->nullable()->after('spam_score');
            $table->boolean('is_suspicious')->default(false)->after('last_flagged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['spam_score', 'last_flagged_at', 'is_suspicious']);
        });
    }
};
