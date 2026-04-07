<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('body');
            $table->enum('type', ['feature', 'update', 'maintenance', 'message', 'promo'])
                  ->default('feature');
            $table->string('version', 20)->nullable();   // e.g. "v2.4"
            $table->string('badge_label', 40)->nullable(); // e.g. "NEW", "HOT", "FIXED"
            $table->string('badge_color', 20)->default('primary'); // Bootstrap color name
            $table->boolean('is_published')->default(false);
            $table->boolean('show_popup')->default(true);  // auto-open modal on next login
            $table->unsignedInteger('target_user_id')->nullable(); // null = all users
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('announcement_id');
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['user_id', 'announcement_id']);
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('announcement_id')->references('id')->on('announcements')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');
        Schema::dropIfExists('announcements');
    }
};
