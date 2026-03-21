<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('premium_payments', 'proof_image')) {
            return; // already exists (added manually on prod)
        }
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->after('tx_hash');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('premium_payments', 'proof_image')) {
            Schema::table('premium_payments', function (Blueprint $table) {
                $table->dropColumn('proof_image');
            });
        }
    }
};
