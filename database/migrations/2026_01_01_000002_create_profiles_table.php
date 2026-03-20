<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('headline', 120)->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->enum('body_type', ['slim', 'athletic', 'average', 'curvy', 'large', 'prefer_not_say'])->nullable();
            $table->string('ethnicity', 60)->nullable();
            $table->string('religion', 60)->nullable();
            $table->enum('education', ['high_school', 'some_college', 'bachelors', 'masters', 'phd', 'trade_school', 'other'])->nullable();
            $table->string('occupation', 100)->nullable();
            $table->enum('relationship_goal', ['casual', 'serious', 'friendship', 'marriage', 'open'])->nullable();
            $table->enum('smoking', ['never', 'occasionally', 'regularly', 'trying_to_quit'])->nullable();
            $table->enum('drinking', ['never', 'socially', 'regularly'])->nullable();
            $table->boolean('has_children')->nullable();
            $table->enum('wants_children', ['yes', 'no', 'open', 'not_sure'])->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
