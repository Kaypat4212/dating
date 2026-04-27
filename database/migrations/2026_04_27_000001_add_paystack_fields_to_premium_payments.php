<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('plan'); // paystack, crypto
            $table->string('paystack_reference')->nullable()->unique()->after('payment_method');
            $table->string('paystack_access_code')->nullable()->after('paystack_reference');
        });
    }

    public function down(): void
    {
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paystack_reference', 'paystack_access_code']);
        });
    }
};
