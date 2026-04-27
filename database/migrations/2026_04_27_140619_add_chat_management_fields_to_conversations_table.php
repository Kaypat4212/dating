<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->boolean('is_pinned_user1')->default(false)->after('disappear_after');
            $table->boolean('is_pinned_user2')->default(false)->after('is_pinned_user1');
            $table->boolean('hidden_for_user1')->default(false)->after('is_pinned_user2');
            $table->boolean('hidden_for_user2')->default(false)->after('hidden_for_user1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['is_pinned_user1', 'is_pinned_user2', 'hidden_for_user1', 'hidden_for_user2']);
        });
    }
};
