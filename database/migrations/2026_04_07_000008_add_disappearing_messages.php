<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add disappear timer setting to conversations
        Schema::table('conversations', function (Blueprint $table) {
            $table->enum('disappear_after', ['off', '1h', '24h', '7d'])->default('off')->after('match_id');
        });

        // Add expiry timestamp to messages
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('read_at');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropColumn('expires_at');
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('disappear_after');
        });
    }
};
