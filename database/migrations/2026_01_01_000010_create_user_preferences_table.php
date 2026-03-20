<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('min_age')->default(18);
            $table->unsignedTinyInteger('max_age')->default(45);
            $table->unsignedSmallInteger('max_distance_km')->default(100);
            $table->enum('seeking_gender', ['male', 'female', 'everyone'])->default('everyone');
            $table->json('body_types')->nullable();
            $table->boolean('show_online_only')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
