<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speed_dating_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user2_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('duration_minutes')->default(5);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['waiting', 'active', 'ended', 'abandoned'])->default('waiting');
            $table->boolean('connect_user1')->default(false);
            $table->boolean('connect_user2')->default(false);
            $table->timestamps();
        });

        Schema::create('speed_dating_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['waiting', 'matched', 'left'])->default('waiting');
            $table->foreignId('room_id')->nullable()->constrained('speed_dating_rooms')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('speed_dating_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('speed_dating_rooms')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speed_dating_messages');
        Schema::dropIfExists('speed_dating_queue');
        Schema::dropIfExists('speed_dating_rooms');
    }
};
