<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->string('message', 200)->nullable()->after('is_super_like');
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->string('preferred_state', 100)->nullable()->after('max_distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropColumn('message');
        });
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn('preferred_state');
        });
    }
};
