<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compatibility_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_text');
            $table->string('category', 40)->nullable();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->json('options'); // JSON array of answer options
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('compatibility_questions')->cascadeOnDelete();
            $table->string('answer_value');
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answers');
        Schema::dropIfExists('compatibility_questions');
    }
};
