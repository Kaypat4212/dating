<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('travel_plans', function (Blueprint $table) {
            $table->string('origin_country', 100)->nullable()->after('destination_country');
            $table->string('from_city', 150)->nullable()->after('origin_country');
        });
    }

    public function down(): void
    {
        Schema::table('travel_plans', function (Blueprint $table) {
            $table->dropColumn(['origin_country', 'from_city']);
        });
    }
};
