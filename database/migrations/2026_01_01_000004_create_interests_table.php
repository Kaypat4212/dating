<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->string('icon', 40)->nullable(); // bootstrap icon name
            $table->timestamps();
        });

        Schema::create('profile_interest', function (Blueprint $table) {
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->primary(['profile_id', 'interest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_interest');
        Schema::dropIfExists('interests');
    }
};
