<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpn_detection_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_vpn')->default(false)->index();
            $table->integer('confidence')->default(0);
            $table->string('provider')->nullable();
            $table->json('detection_details')->nullable();
            $table->string('action_taken', 50)->default('logged'); // blocked, allowed, logged
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Add index for reporting queries
            $table->index(['created_at', 'is_vpn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpn_detection_logs');
    }
};
