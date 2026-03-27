<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('voice_prompt_questions', function (Blueprint $table) {
            $table->id();
            $table->string('prompt_text');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        Schema::create('voice_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('voice_prompt_questions')->cascadeOnDelete();
            $table->string('audio_path');
            $table->integer('duration_seconds')->default(0);
            $table->boolean('show_on_profile')->default(true);
            $table->integer('plays_count')->default(0);
            $table->timestamps();
            $table->index('user_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('voice_prompts');
        Schema::dropIfExists('voice_prompt_questions');
    }
};
