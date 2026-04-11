<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_records', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->unique();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('source')->default('scan');
            $table->string('status')->default('available');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamp('file_created_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->unsignedBigInteger('restored_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'file_created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_records');
    }
};