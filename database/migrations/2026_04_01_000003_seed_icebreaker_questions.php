<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Only seed if table is empty (safe to run on existing installs)
        if (DB::table('icebreaker_questions')->exists()) {
            return;
        }

        $now = now();

        DB::table('icebreaker_questions')->insert([
            // Would You Rather
            ['question' => 'Would you rather travel to 10 countries for a week each or live in 1 foreign country for a year?', 'type' => 'would_you_rather', 'option_a' => '10 countries for a week', 'option_b' => '1 country for a year', 'is_active' => true, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Would you rather have a home on the beach or in the mountains?', 'type' => 'would_you_rather', 'option_a' => 'Beach house', 'option_b' => 'Mountain cabin', 'is_active' => true, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Would you rather always know the truth or never have to lie?', 'type' => 'would_you_rather', 'option_a' => 'Always know the truth', 'option_b' => 'Never have to lie', 'is_active' => true, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Would you rather have an extra hour every day or an extra day every week?', 'type' => 'would_you_rather', 'option_a' => 'Extra hour each day', 'option_b' => 'Extra day each week', 'is_active' => true, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Would you rather have a soulmate or be wildly successful in your career?', 'type' => 'would_you_rather', 'option_a' => 'Find your soulmate', 'option_b' => 'Career success', 'is_active' => true, 'order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Would you rather give up social media or coffee for a year?', 'type' => 'would_you_rather', 'option_a' => 'Give up social media', 'option_b' => 'Give up coffee', 'is_active' => true, 'order' => 6, 'created_at' => $now, 'updated_at' => $now],

            // Two Truths & a Lie
            ['question' => 'Share two truths and one lie about yourself. Make it interesting!', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Tell me two real experiences and one made-up one about your travels.', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 11, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Two things you\'ve actually eaten and one you haven\'t (but sounds plausible).', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 12, 'created_at' => $now, 'updated_at' => $now],

            // This or That
            ['question' => 'Cats or dogs?', 'type' => 'this_or_that', 'option_a' => 'Cats', 'option_b' => 'Dogs', 'is_active' => true, 'order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Early bird or night owl?', 'type' => 'this_or_that', 'option_a' => 'Early bird', 'option_b' => 'Night owl', 'is_active' => true, 'order' => 21, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Netflix night in or going out?', 'type' => 'this_or_that', 'option_a' => 'Netflix night in', 'option_b' => 'Going out', 'is_active' => true, 'order' => 22, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Cooking at home or eating out?', 'type' => 'this_or_that', 'option_a' => 'Cooking at home', 'option_b' => 'Eating out', 'is_active' => true, 'order' => 23, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Texting or calling?', 'type' => 'this_or_that', 'option_a' => 'Texting', 'option_b' => 'Calling', 'is_active' => true, 'order' => 24, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Summer or winter?', 'type' => 'this_or_that', 'option_a' => 'Summer', 'option_b' => 'Winter', 'is_active' => true, 'order' => 25, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'City life or countryside?', 'type' => 'this_or_that', 'option_a' => 'City life', 'option_b' => 'Countryside', 'is_active' => true, 'order' => 26, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Spontaneous or planner?', 'type' => 'this_or_that', 'option_a' => 'Spontaneous', 'option_b' => 'Planner', 'is_active' => true, 'order' => 27, 'created_at' => $now, 'updated_at' => $now],

            // Open-Ended
            ['question' => 'What\'s the most embarrassing song on your playlist?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'What\'s the best piece of advice you\'ve ever received?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 31, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'What would your autobiography be called?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 32, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'What\'s something on your bucket list?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 33, 'created_at' => $now, 'updated_at' => $now],
            ['question' => 'Describe your perfect first date in 3 words.', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'is_active' => true, 'order' => 34, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('icebreaker_questions')->truncate();
    }
};
