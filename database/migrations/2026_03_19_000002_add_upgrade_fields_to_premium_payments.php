<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->boolean('is_upgrade')->default(false)->after('status');
            $table->string('upgrade_from_plan', 20)->nullable()->after('is_upgrade');
            $table->decimal('upgrade_credit', 8, 2)->nullable()->after('upgrade_from_plan');
            $table->string('invoice_number', 30)->nullable()->unique()->after('upgrade_credit');
        });
    }

    public function down(): void
    {
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->dropColumn(['is_upgrade', 'upgrade_from_plan', 'upgrade_credit', 'invoice_number']);
        });
    }
};
