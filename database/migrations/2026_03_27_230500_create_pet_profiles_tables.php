<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['dog','cat','bird','rabbit','fish','reptile','hamster','other']);
            $table->string('breed')->nullable();
            $table->integer('age_years')->nullable();
            $table->integer('age_months')->nullable();
            $table->enum('size', ['tiny','small','medium','large','extra_large'])->nullable();
            $table->text('about')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('show_on_profile')->default(true);
            $table->timestamps();
            $table->index('user_id');
        });
    }
    public function down(): void { Schema::dropIfExists('pets'); }
};
