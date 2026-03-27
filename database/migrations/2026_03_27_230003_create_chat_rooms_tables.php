<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chat Rooms
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // emoji
            $table->string('cover_image')->nullable();
            $table->enum('type', ['public', 'private', 'premium'])->default('public');
            $table->integer('max_participants')->default(100);
            $table->integer('active_participants')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('rules')->nullable(); // Room rules/guidelines
            $table->json('settings')->nullable(); // Slow mode, etc.
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });

        // Chat Room Messages
        Schema::create('chat_room_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->enum('type', ['text', 'image', 'gif', 'emoji', 'system'])->default('text');
            $table->json('attachments')->nullable(); // Images, GIFs
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->foreignId('reply_to_id')->nullable()->constrained('chat_room_messages')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['room_id', 'created_at']);
            $table->index('is_deleted');
        });

        // Chat Room Participants
        Schema::create('chat_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'moderator', 'admin'])->default('member');
            $table->boolean('is_online')->default(false);
            $table->timestamp('joined_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_muted')->default(false); // User muted the room
            $table->boolean('is_banned')->default(false); // User banned from room
            $table->timestamps();
            
            $table->unique(['room_id', 'user_id']);
            $table->index('is_online');
        });

        // Chat Room Reactions (for messages)
        Schema::create('chat_room_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_room_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 10); // 👍, ❤️, 😂, etc.
            $table->timestamps();
            
            $table->unique(['message_id', 'user_id', 'emoji']);
        });

        // Chat Room Moderation Log
        Schema::create('chat_room_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Target user
            $table->enum('action', ['mute', 'unmute', 'kick', 'ban', 'unban', 'delete_message', 'pin_message']);
            $table->text('reason')->nullable();
            $table->json('meta')->nullable(); // Duration, message_id, etc.
            $table->timestamps();
            
            $table->index(['room_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_moderation_logs');
        Schema::dropIfExists('chat_room_reactions');
        Schema::dropIfExists('chat_room_participants');
        Schema::dropIfExists('chat_room_messages');
        Schema::dropIfExists('chat_rooms');
    }
};
