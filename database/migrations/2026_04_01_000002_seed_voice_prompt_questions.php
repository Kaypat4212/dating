<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Only seed if the table is empty (safe to run on existing installs)
        if (DB::table('voice_prompt_questions')->exists()) {
            return;
        }

        $now = now();

        DB::table('voice_prompt_questions')->insert([
            ['prompt_text' => 'My most controversial opinion is...', 'is_active' => true, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'The way to my heart is...', 'is_active' => true, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'I geek out about...', 'is_active' => true, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'The best travel story I have is...', 'is_active' => true, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'Something I find hilarious is...', 'is_active' => true, 'order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => "I'm convinced that...", 'is_active' => true, 'order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'My hype song is...', 'is_active' => true, 'order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'A fun fact about me is...', 'is_active' => true, 'order' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'My morning routine sounds like...', 'is_active' => true, 'order' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'The last thing that made me laugh out loud...', 'is_active' => true, 'order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'My love language is...', 'is_active' => true, 'order' => 11, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'Two truths and a lie about me...', 'is_active' => true, 'order' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => "I'd be described as a... by my friends", 'is_active' => true, 'order' => 13, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'The perfect Sunday for me looks like...', 'is_active' => true, 'order' => 14, 'created_at' => $now, 'updated_at' => $now],
            ['prompt_text' => 'My biggest red flag is probably...', 'is_active' => true, 'order' => 15, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('voice_prompt_questions')->truncate();
    }
};
