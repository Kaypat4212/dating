<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voice_calls', function (Blueprint $table) {
            // Daily.co room URL (full URL returned by Daily.co REST API)
            $table->string('room_url', 500)->nullable()->after('channel_name');
            // Call type — voice (audio only) or video
            $table->enum('call_type', ['voice', 'video'])->default('voice')->after('room_url');
        });
    }

    public function down(): void
    {
        Schema::table('voice_calls', function (Blueprint $table) {
            $table->dropColumn(['room_url', 'call_type']);
        });
    }
};
