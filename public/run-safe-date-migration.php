<?php
/**
 * One-time migration runner for safe_date_checkins (and related tables).
 * DELETE THIS FILE after running it.
 */

// Basic protection — only allow from your own IP or with a secret token
$secret = $_GET['token'] ?? '';
if ($secret !== 'hc_migrate_2026') {
    http_response_code(403);
    die('Forbidden');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre>';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

$created = [];

// 1. profile_passes
if (! Schema::hasTable('profile_passes')) {
    Schema::create('profile_passes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('passer_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('passed_id')->constrained('users')->cascadeOnDelete();
        $table->timestamp('passed_at')->useCurrent();
        $table->boolean('resurfaced')->default(false);
        $table->unique(['passer_id', 'passed_id']);
    });
    $created[] = 'profile_passes';
}

// 2. match_questions
if (! Schema::hasTable('match_questions')) {
    Schema::create('match_questions', function (Blueprint $table) {
        $table->id();
        $table->string('question', 250);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    $created[] = 'match_questions';
}

// 3. match_question_answers
if (! Schema::hasTable('match_question_answers')) {
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
    $created[] = 'match_question_answers';
}

// 4. safe_date_checkins
if (! Schema::hasTable('safe_date_checkins')) {
    Schema::create('safe_date_checkins', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('date_location', 200)->nullable();
        $table->string('emergency_contact_name', 80)->nullable();
        $table->string('emergency_contact_phone', 30)->nullable();
        $table->string('emergency_contact_email', 100)->nullable();
        $table->timestamp('date_at');
        $table->unsignedSmallInteger('checkin_minutes')->default(120);
        $table->timestamp('checked_in_at')->nullable();
        $table->timestamp('alert_sent_at')->nullable();
        $table->enum('status', ['active', 'safe', 'alerted'])->default('active');
        $table->timestamps();
    });
    $created[] = 'safe_date_checkins';
}

// 5. Seed match_questions if empty
if (DB::table('match_questions')->count() === 0) {
    DB::table('match_questions')->insert([
        ['question' => 'If you could teleport anywhere right now, where would you go?',          'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's your idea of a perfect first date?",                              'created_at' => now(), 'updated_at' => now()],
        ['question' => 'Coffee ☕ or tea 🍵 — and how do you take it?',                         'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's a skill you're proud of that most people wouldn't guess?",        'created_at' => now(), 'updated_at' => now()],
        ['question' => 'Morning person or night owl? 🌅🦉',                                     'created_at' => now(), 'updated_at' => now()],
        ['question' => "What song are you embarrassed to admit you love?",                       'created_at' => now(), 'updated_at' => now()],
        ['question' => 'Describe your dream weekend in three words.',                            'created_at' => now(), 'updated_at' => now()],
        ['question' => 'If your life was a movie, what genre would it be?',                     'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's your go-to comfort food?",                                        'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's the most spontaneous thing you've ever done?",                    'created_at' => now(), 'updated_at' => now()],
        ['question' => 'Would you rather explore space 🚀 or the deep ocean 🌊?',               'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's the last thing that made you laugh out loud?",                    'created_at' => now(), 'updated_at' => now()],
        ['question' => 'Beach 🏖️, mountains 🏔️, or city 🌆?',                                 'created_at' => now(), 'updated_at' => now()],
        ['question' => "What's on your bucket list for this year?",                              'created_at' => now(), 'updated_at' => now()],
    ]);
    $created[] = 'match_questions (seeded)';
}

if (empty($created)) {
    echo "✅ All tables already exist — nothing to create.\n";
} else {
    foreach ($created as $t) {
        echo "✅ Created: {$t}\n";
    }
}

echo "\n⚠️  DELETE this file from the server now!\n";
echo '</pre>';
