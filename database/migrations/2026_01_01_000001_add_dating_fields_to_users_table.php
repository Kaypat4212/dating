<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('gender', ['male', 'female', 'non_binary', 'other'])->nullable()->after('email');
            $table->enum('seeking', ['male', 'female', 'everyone'])->nullable()->after('gender');
            $table->date('date_of_birth')->nullable()->after('seeking');
            $table->boolean('is_premium')->default(false)->after('date_of_birth');
            $table->timestamp('premium_expires_at')->nullable()->after('is_premium');
            $table->boolean('is_banned')->default(false)->after('premium_expires_at');
            $table->string('banned_reason')->nullable()->after('is_banned');
            $table->timestamp('last_active_at')->nullable()->after('banned_reason');
            $table->boolean('profile_complete')->default(false)->after('last_active_at');
            $table->integer('onboarding_step')->default(0)->after('profile_complete');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'gender', 'seeking', 'date_of_birth',
                'is_premium', 'premium_expires_at', 'is_banned', 'banned_reason',
                'last_active_at', 'profile_complete', 'onboarding_step',
            ]);
        });
    }
};
