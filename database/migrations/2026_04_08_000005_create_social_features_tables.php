<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Passes (left-swipes) — so we can resurface them after 30 days
        Schema::create('profile_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('passed_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('passed_at')->useCurrent();
            $table->boolean('resurfaced')->default(false); // has it appeared in second-chance queue?
            $table->unique(['passer_id', 'passed_id']);
        });

        // Daily match question (one active per day)
        Schema::create('match_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question', 250);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Answers from matched users
        Schema::create('match_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('match_questions')->cascadeOnDelete();
            $table->string('answer', 500);
            $table->date('answered_date');
            $table->timestamps();
            $table->unique(['match_id', 'user_id', 'question_id']);
        });

        // Safe Date Check-In
        Schema::create('safe_date_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('date_location', 200)->nullable();
            $table->string('emergency_contact_name', 80)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_email', 100)->nullable();
            $table->timestamp('date_at');            // when date starts
            $table->unsignedSmallInteger('checkin_minutes')->default(120); // alert after X minutes
            $table->timestamp('checked_in_at')->nullable();  // user confirmed safe
            $table->timestamp('alert_sent_at')->nullable();  // emergency alert fired
            $table->enum('status', ['active', 'safe', 'alerted'])->default('active');
            $table->timestamps();
        });

        // Seed a few match questions
        \Illuminate\Support\Facades\DB::table('match_questions')->insert([
            ['question' => 'If you could teleport anywhere right now, where would you go?', 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s your idea of a perfect first date?',                    'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Coffee ☕ or tea 🍵 — and how do you take it?',                 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s a skill you\'re proud of that most people wouldn\'t guess?', 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Morning person or night owl? 🌅🦉',                            'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What song are you embarrassed to admit you love?',             'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Describe your dream weekend in three words.',                  'created_at' => now(), 'updated_at' => now()],
            ['question' => 'If your life was a movie, what genre would it be?',            'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s your go-to comfort food?',                            'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s the most spontaneous thing you\'ve ever done?',       'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Would you rather explore space 🚀 or the deep ocean 🌊?',     'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s the last thing that made you laugh out loud?',        'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Beach 🏖️, mountains 🏔️, or city 🌆?',                       'created_at' => now(), 'updated_at' => now()],
            ['question' => 'What\'s on your bucket list for this year?',                  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('safe_date_checkins');
        Schema::dropIfExists('match_question_answers');
        Schema::dropIfExists('match_questions');
        Schema::dropIfExists('profile_passes');
    }
};
