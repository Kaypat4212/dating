<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_agent', 512)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('isp', 200)->nullable();
            $table->string('org', 200)->nullable();
            $table->boolean('is_proxy')->default(false);
            $table->string('browser', 80)->nullable();
            $table->timestamp('visited_at')->useCurrent();
            $table->timestamps();

            $table->index('ip_address');
            $table->index('country_code');
            $table->index('visited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_visits');
    }
};
