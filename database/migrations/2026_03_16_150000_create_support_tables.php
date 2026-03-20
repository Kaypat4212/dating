<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Support Conversations ────────────────────────────────────────────
        Schema::create('support_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->enum('status', ['open', 'waiting', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->unsignedInteger('unread_admin')->default(0);   // unread for admin
            $table->unsignedInteger('unread_user')->default(0);    // unread for user
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        // ── Support Messages ─────────────────────────────────────────────────
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'admin', 'bot'])->default('user');
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('support_conversation_id');
        });

        // ── Support Auto-Responses ───────────────────────────────────────────
        Schema::create('support_auto_responses', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_keyword');   // keyword to match in user message
            $table->text('response_text');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('priority')->default(0); // higher wins on conflict
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_conversations');
        Schema::dropIfExists('support_auto_responses');
    }
};
