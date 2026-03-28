<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->string('country_code', 10)->nullable()->after('requires_verified')
                ->comment('ISO 3166-1 alpha-2 code — if set, only users with matching country can post');
        });
    }

    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
};
