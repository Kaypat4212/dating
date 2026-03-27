<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunityDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Voice Prompt Questions ─────────────────────────────────────────────
        $voicePrompts = [
            ['prompt_text' => "My most controversial opinion is...", 'order' => 1],
            ['prompt_text' => "The way to my heart is...", 'order' => 2],
            ['prompt_text' => "I geek out about...", 'order' => 3],
            ['prompt_text' => "The best travel story I have is...", 'order' => 4],
            ['prompt_text' => "Something I find hilarious is...", 'order' => 5],
            ['prompt_text' => "I'm convinced that...", 'order' => 6],
            ['prompt_text' => "My hype song is...", 'order' => 7],
            ['prompt_text' => "A fun fact about me is...", 'order' => 8],
            ['prompt_text' => "My morning routine sounds like...", 'order' => 9],
            ['prompt_text' => "The last thing that made me laugh out loud...", 'order' => 10],
            ['prompt_text' => "My love language is...", 'order' => 11],
            ['prompt_text' => "Two truths and a lie about me...", 'order' => 12],
            ['prompt_text' => "I'd be described as a... by my friends", 'order' => 13],
            ['prompt_text' => "The perfect Sunday for me looks like...", 'order' => 14],
            ['prompt_text' => "My biggest red flag is probably...", 'order' => 15],
        ];

        foreach ($voicePrompts as $prompt) {
            DB::table('voice_prompt_questions')->updateOrInsert(
                ['prompt_text' => $prompt['prompt_text']],
                array_merge($prompt, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // ── Icebreaker Questions ───────────────────────────────────────────────
        $icebreakers = [
            // Would You Rather
            ['question' => 'Would you rather travel to 10 countries for a week each or live in 1 foreign country for a year?', 'type' => 'would_you_rather', 'option_a' => '10 countries for a week', 'option_b' => '1 country for a year', 'order' => 1],
            ['question' => 'Would you rather have a home on the beach or in the mountains?', 'type' => 'would_you_rather', 'option_a' => 'Beach house', 'option_b' => 'Mountain cabin', 'order' => 2],
            ['question' => 'Would you rather always know the truth or never have to lie?', 'type' => 'would_you_rather', 'option_a' => 'Always know the truth', 'option_b' => 'Never have to lie', 'order' => 3],
            ['question' => 'Would you rather have an extra hour every day or an extra day every week?', 'type' => 'would_you_rather', 'option_a' => 'Extra hour each day', 'option_b' => 'Extra day each week', 'order' => 4],
            ['question' => 'Would you rather have a soulmate or be wildly successful in your career?', 'type' => 'would_you_rather', 'option_a' => 'Find your soulmate', 'option_b' => 'Career success', 'order' => 5],
            ['question' => 'Would you rather give up social media or coffee for a year?', 'type' => 'would_you_rather', 'option_a' => 'Give up social media', 'option_b' => 'Give up coffee', 'order' => 6],

            // Two Truths & a Lie
            ['question' => 'Share two truths and one lie about yourself. Make it interesting!', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'order' => 10],
            ['question' => 'Tell me two real experiences and one made-up one about your travels.', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'order' => 11],
            ['question' => 'Two things you\'ve actually eaten and one you haven\'t (but sounds plausible).', 'type' => 'two_truths_lie', 'option_a' => null, 'option_b' => null, 'order' => 12],

            // This or That
            ['question' => 'Cats or dogs?', 'type' => 'this_or_that', 'option_a' => 'Cats', 'option_b' => 'Dogs', 'order' => 20],
            ['question' => 'Early bird or night owl?', 'type' => 'this_or_that', 'option_a' => 'Early bird', 'option_b' => 'Night owl', 'order' => 21],
            ['question' => 'Netflix night in or going out?', 'type' => 'this_or_that', 'option_a' => 'Netflix night in', 'option_b' => 'Going out', 'order' => 22],
            ['question' => 'Cooking at home or eating out?', 'type' => 'this_or_that', 'option_a' => 'Cooking at home', 'option_b' => 'Eating out', 'order' => 23],
            ['question' => 'Texting or calling?', 'type' => 'this_or_that', 'option_a' => 'Texting', 'option_b' => 'Calling', 'order' => 24],
            ['question' => 'Summer or winter?', 'type' => 'this_or_that', 'option_a' => 'Summer', 'option_b' => 'Winter', 'order' => 25],
            ['question' => 'City life or countryside?', 'type' => 'this_or_that', 'option_a' => 'City life', 'option_b' => 'Countryside', 'order' => 26],
            ['question' => 'Spontaneous or planner?', 'type' => 'this_or_that', 'option_a' => 'Spontaneous', 'option_b' => 'Planner', 'order' => 27],

            // Open-Ended
            ['question' => 'What\'s the most embarrassing song on your playlist?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'order' => 30],
            ['question' => 'What\'s the best piece of advice you\'ve ever received?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'order' => 31],
            ['question' => 'What would your autobiography be called?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'order' => 32],
            ['question' => 'What\'s something on your bucket list?', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'order' => 33],
            ['question' => 'Describe your perfect first date in 3 words.', 'type' => 'open_ended', 'option_a' => null, 'option_b' => null, 'order' => 34],
        ];

        foreach ($icebreakers as $q) {
            DB::table('icebreaker_questions')->updateOrInsert(
                ['question' => $q['question']],
                array_merge($q, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // ── Blog Categories ────────────────────────────────────────────────────
        $blogCats = [
            ['name' => 'Dating Tips', 'slug' => 'dating-tips', 'description' => 'Practical advice for modern dating', 'icon' => 'bi bi-heart', 'color' => '#e91e8c', 'order' => 1],
            ['name' => 'Relationships', 'slug' => 'relationships', 'description' => 'Building and nurturing meaningful connections', 'icon' => 'bi bi-people-fill', 'color' => '#0d6efd', 'order' => 2],
            ['name' => 'Safety Tips', 'slug' => 'safety-tips', 'description' => 'Stay safe when dating online and offline', 'icon' => 'bi bi-shield-check', 'color' => '#198754', 'order' => 3],
            ['name' => 'Success Stories', 'slug' => 'success-stories', 'description' => 'Real couples who found love here', 'icon' => 'bi bi-stars', 'color' => '#ffc107', 'order' => 4],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Dating, travel, food, and life', 'icon' => 'bi bi-sun', 'color' => '#fd7e14', 'order' => 5],
            ['name' => 'News & Updates', 'slug' => 'news', 'description' => 'Platform news and feature announcements', 'icon' => 'bi bi-megaphone', 'color' => '#6f42c1', 'order' => 6],
        ];

        foreach ($blogCats as $cat) {
            DB::table('blog_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // ── Forum Categories ───────────────────────────────────────────────────
        $forumCats = [
            ['name' => 'General Discussion', 'slug' => 'general', 'description' => 'Anything and everything', 'icon' => 'bi bi-chat-dots', 'color' => '#0d6efd', 'order' => 1, 'requires_verified' => false],
            ['name' => 'Dating Advice', 'slug' => 'dating-advice', 'description' => 'Ask for and give relationship advice', 'icon' => 'bi bi-heart', 'color' => '#e91e8c', 'order' => 2, 'requires_verified' => false],
            ['name' => 'First Date Ideas', 'slug' => 'first-date-ideas', 'description' => 'Creative date ideas and inspiration', 'icon' => 'bi bi-calendar-heart', 'color' => '#fd7e14', 'order' => 3, 'requires_verified' => false],
            ['name' => 'Travel & Adventure', 'slug' => 'travel', 'description' => 'Share travel experiences and find travel buddies', 'icon' => 'bi bi-airplane', 'color' => '#20c997', 'order' => 4, 'requires_verified' => false],
            ['name' => 'Pets & Animals', 'slug' => 'pets', 'description' => 'Share your pet stories and photos', 'icon' => 'bi bi-emoji-smile', 'color' => '#ffc107', 'order' => 5, 'requires_verified' => false],
            ['name' => 'Success Stories', 'slug' => 'success-stories', 'description' => 'Share how you found your match', 'icon' => 'bi bi-stars', 'color' => '#198754', 'order' => 6, 'requires_verified' => false],
            ['name' => 'Rants & Venting', 'slug' => 'rants', 'description' => 'Safe space to vent about dating', 'icon' => 'bi bi-emoji-angry', 'color' => '#dc3545', 'order' => 7, 'requires_verified' => false],
        ];

        foreach ($forumCats as $cat) {
            DB::table('forum_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Community data seeded: voice prompts, icebreakers, blog categories, forum categories.');
    }
}
