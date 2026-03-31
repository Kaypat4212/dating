<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
            // track if the referrer has been rewarded (e.g. credits)
            $table->boolean('rewarded')->default(false);
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->unique('referred_id'); // one referrer per new user
            $table->index('referrer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
