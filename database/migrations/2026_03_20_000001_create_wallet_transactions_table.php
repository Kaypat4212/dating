<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'tip_sent',
                'tip_received',
                'deposit',
                'withdrawal',
                'admin_credit',
                'admin_debit',
            ]);
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('balance_after')->default(0);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
