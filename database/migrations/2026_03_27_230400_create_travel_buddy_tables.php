<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('travel_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('destination');
            $table->string('destination_country')->nullable();
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->date('travel_from');
            $table->date('travel_to');
            $table->enum('travel_type', ['solo','with_friends','seeking_companion'])->default('seeking_companion');
            $table->text('description')->nullable();
            $table->json('interests')->nullable();
            $table->enum('accommodation', ['hotel','hostel','airbnb','camping','flexible'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->index(['user_id','is_active']);
            $table->index(['destination','travel_from','travel_to']);
        });
        Schema::create('travel_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('travel_plans')->cascadeOnDelete();
            $table->timestamp('expressed_at')->useCurrent();
            $table->enum('status', ['pending','accepted','declined'])->default('pending');
            $table->timestamps();
            $table->unique(['user_id','plan_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('travel_interests');
        Schema::dropIfExists('travel_plans');
    }
};
