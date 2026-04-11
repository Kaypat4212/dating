<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->string('category', 50)->default('general'); // general, snap, call, chat, profile, payment, other
            $table->string('page_url', 500)->nullable();
            $table->string('browser', 200)->nullable();
            $table->string('status', 30)->default('open'); // open, in_progress, resolved, closed
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
