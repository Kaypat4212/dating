<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('icebreaker_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->enum('type', ['would_you_rather','two_truths_lie','this_or_that','open_ended'])->default('would_you_rather');
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        Schema::create('icebreaker_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('icebreaker_questions')->cascadeOnDelete();
            $table->string('answer')->nullable();
            $table->enum('choice', ['a','b'])->nullable();
            $table->boolean('show_on_profile')->default(true);
            $table->timestamps();
            $table->unique(['user_id','question_id']);
        });
        Schema::create('icebreaker_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_a_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_b_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->foreignId('current_question_id')->nullable()->constrained('icebreaker_questions')->nullOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->unique(['user_a_id','user_b_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('icebreaker_sessions');
        Schema::dropIfExists('icebreaker_answers');
        Schema::dropIfExists('icebreaker_questions');
    }
};
