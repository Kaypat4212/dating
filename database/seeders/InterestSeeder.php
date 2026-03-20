<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InterestSeeder extends Seeder
{
    public function run(): void
    {
        $interests = [
            // Outdoors
            ['name' => 'Hiking',        'icon' => 'bi-tree'],
            ['name' => 'Camping',       'icon' => 'bi-moon-stars'],
            ['name' => 'Rock Climbing', 'icon' => 'bi-triangle'],
            ['name' => 'Cycling',       'icon' => 'bi-bicycle'],
            ['name' => 'Running',       'icon' => 'bi-person-walking'],
            ['name' => 'Swimming',      'icon' => 'bi-water'],
            ['name' => 'Surfing',       'icon' => 'bi-wind'],
            ['name' => 'Skiing',        'icon' => 'bi-snow'],

            // Arts & Culture
            ['name' => 'Photography',   'icon' => 'bi-camera'],
            ['name' => 'Painting',      'icon' => 'bi-palette'],
            ['name' => 'Drawing',       'icon' => 'bi-pencil'],
            ['name' => 'Museums',       'icon' => 'bi-building'],
            ['name' => 'Theater',       'icon' => 'bi-masks-theater'],
            ['name' => 'Dancing',       'icon' => 'bi-music-note-beamed'],

            // Food & Drink
            ['name' => 'Cooking',       'icon' => 'bi-egg-fried'],
            ['name' => 'Baking',        'icon' => 'bi-cake'],
            ['name' => 'Wine Tasting',  'icon' => 'bi-cup'],
            ['name' => 'Coffee',        'icon' => 'bi-cup-hot'],
            ['name' => 'Foodie',        'icon' => 'bi-fork-knife'],

            // Music
            ['name' => 'Playing Guitar',    'icon' => 'bi-music-note'],
            ['name' => 'Playing Piano',     'icon' => 'bi-music-note-list'],
            ['name' => 'Live Music',        'icon' => 'bi-headphones'],
            ['name' => 'Singing',           'icon' => 'bi-mic'],

            // Tech & Gaming
            ['name' => 'Gaming',        'icon' => 'bi-controller'],
            ['name' => 'Board Games',   'icon' => 'bi-grid-3x3-gap'],
            ['name' => 'Technology',    'icon' => 'bi-cpu'],
            ['name' => 'Coding',        'icon' => 'bi-code-slash'],

            // Wellness
            ['name' => 'Yoga',          'icon' => 'bi-peace'],
            ['name' => 'Meditation',    'icon' => 'bi-heart-pulse'],
            ['name' => 'Fitness',       'icon' => 'bi-activity'],

            // Social
            ['name' => 'Traveling',     'icon' => 'bi-airplane'],
            ['name' => 'Volunteering',  'icon' => 'bi-hand-thumbs-up'],
            ['name' => 'Pets',          'icon' => 'bi-heart'],
            ['name' => 'Books',         'icon' => 'bi-book'],
            ['name' => 'Movies',        'icon' => 'bi-film'],
            ['name' => 'TV Shows',      'icon' => 'bi-tv'],
            ['name' => 'Astronomy',     'icon' => 'bi-stars'],
        ];

        foreach ($interests as $data) {
            Interest::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'icon' => $data['icon'],
                ]
            );
        }
    }
}
