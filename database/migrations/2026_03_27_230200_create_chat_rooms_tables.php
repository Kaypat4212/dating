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
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('avatar')->nullable(); // Room image
            $table->enum('type', ['public', 'private', 'interest', 'location'])->default('public');
            $table->integer('max_members')->default(100);
            $table->integer('members_count')->default(0);
            $table->integer('messages_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false); // For private rooms
            $table->json('interests')->nullable(); // Related interests for matching
            $table->string('location')->nullable(); // City/country for location-based rooms
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('location');
        });

        // Chat Room Members
        Schema::create('chat_room_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'moderator', 'admin'])->default('member');
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            
            $table->unique(['room_id', 'user_id']);
            $table->index('role');
        });

        // Chat Room Messages
        Schema::create('chat_room_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained('chat_room_messages')->nullOnDelete();
            $table->text('content');
            $table->enum('type', ['text', 'image', 'gif', 'system'])->default('text');
            $table->string('attachment_url')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            
            $table->index(['room_id', 'created_at']);
            $table->index('is_deleted');
        });

        // Chat Room Message Reactions
        Schema::create('chat_room_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_room_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 10); // ❤️, 👍, 😂, etc.
            $table->timestamps();
            
            $table->unique(['message_id', 'user_id', 'emoji']);
        });

        // Chat Room Invites (for private rooms)
        Schema::create('chat_room_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->unique(['room_id', 'invitee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_invites');
        Schema::dropIfExists('chat_room_message_reactions');
        Schema::dropIfExists('chat_room_messages');
        Schema::dropIfExists('chat_room_members');
        Schema::dropIfExists('chat_rooms');
    }
};
