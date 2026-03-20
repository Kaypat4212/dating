<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 20); // BTC, ETH, USDT, etc.
            $table->string('network', 40)->nullable(); // mainnet, TRC20, ERC20 etc.
            $table->string('address');
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('premium_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan'); // 30day, 90day, 365day
            $table->decimal('amount', 18, 8);
            $table->string('crypto_currency', 20);
            $table->string('wallet_address');
            $table->string('tx_hash')->nullable(); // transaction hash proof
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_payments');
        Schema::dropIfExists('crypto_wallets');
    }
};
