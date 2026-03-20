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
        Schema::table('wallet_withdrawal_requests', function (Blueprint $table) {
            $table->string('currency', 20)->nullable()->after('destination');
            $table->string('network', 60)->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['currency', 'network']);
        });
    }
};
