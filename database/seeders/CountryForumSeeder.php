<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountryForumSeeder extends Seeder
{
    /**
     * Country forum categories with seed topics.
     * country_code matches Profile->country (ISO 3166-1 alpha-2).
     */
    public function run(): void
    {
        $adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first()
                  ?? User::first();

        if (! $adminUser) {
            $this->command->warn('No users found — skipping CountryForumSeeder.');
            return;
        }

        $countries = [
            [
                'flag'         => '🇬🇧',
                'name'         => 'UK Dating & Chat',
                'country_code' => 'GB',
                'description'  => 'Meet locals, discuss UK dating culture, events and relationships.',
                'color'        => '#003087',
                'topics'       => [
                    ['title' => 'Best date ideas in London 2026', 'content' => '<p>London is full of amazing date spots! From the Southbank walk to the Barbican, what are your favourite low-key date ideas in the city?</p>'],
                    ['title' => 'Dating apps in the UK — honest review thread', 'content' => '<p>Which apps are actually working for people here in 2026? Share your honest experiences below.</p>'],
                    ['title' => 'How British dating culture has changed post-pandemic', 'content' => '<p>It feels like people are more intentional about who they spend time with now. Has anyone else noticed a shift in attitudes?</p>'],
                ],
            ],
            [
                'flag'         => '🇳🇬',
                'name'         => 'Nigeria Dating & Relationships',
                'country_code' => 'NG',
                'description'  => 'Connect with Nigerians, discuss culture, dating and modern relationships.',
                'color'        => '#008751',
                'topics'       => [
                    ['title' => 'Navigating modern dating in Lagos 2026', 'content' => '<p>Dating in Lagos is unique — family expectations, career ambitions and social media all play a role. Let\'s discuss.</p>'],
                    ['title' => 'Long-distance within Nigeria — what works?', 'content' => '<p>Many of us move between Abuja, Lagos and Port Harcourt for work. How do you maintain a relationship across cities?</p>'],
                    ['title' => 'Cultural expectations vs personal choices in Nigerian relationships', 'content' => '<p>How do you balance family expectations (especially around marriage) with your own desires in 2026?</p>'],
                ],
            ],
            [
                'flag'         => '🇺🇸',
                'name'         => 'USA Dating & Lifestyle',
                'country_code' => 'US',
                'description'  => 'US-based members discuss dating trends, city life and finding connections.',
                'color'        => '#B22234',
                'topics'       => [
                    ['title' => 'Dating in NYC vs LA — the real difference', 'content' => '<p>Coasts have different energy. New Yorkers are fast, LA is laid-back. Which city has the better dating scene?</p>'],
                    ['title' => 'Is "situationship" culture finally dying out?', 'content' => '<p>2026 feels like a year of people wanting clarity and commitment. Are you seeing that in your own experiences?</p>'],
                    ['title' => 'How to meet people IRL in American cities (not just apps)', 'content' => '<p>Apps are exhausting. What real-world social events, clubs or hobbies have actually helped you meet romantic prospects?</p>'],
                ],
            ],
            [
                'flag'         => '🇨🇦',
                'name'         => 'Canada Dating Hub',
                'country_code' => 'CA',
                'description'  => 'Canadian members share dating stories, advice and local events.',
                'color'        => '#FF0000',
                'topics'       => [
                    ['title' => 'Dating in Canadian winter — tips & ideas', 'content' => '<p>When it\'s -20°C outside, where do Canadians actually go on first dates? Let\'s build the ultimate cold-weather date list.</p>'],
                    ['title' => 'Multicultural dating in Toronto & Vancouver', 'content' => '<p>Canada\'s diverse cities mean dating across cultures is common. What have been your experiences navigating cultural differences?</p>'],
                ],
            ],
            [
                'flag'         => '🇦🇺',
                'name'         => 'Australia Dating & Social',
                'country_code' => 'AU',
                'description'  => 'Aussie members discuss relationships, beach meets and local dating culture.',
                'color'        => '#00008B',
                'topics'       => [
                    ['title' => 'Outdoor date ideas across Australia', 'content' => '<p>From the Great Barrier Reef to the Blue Mountains — Australia is perfect for outdoor dates. What are your go-to spots?</p>'],
                    ['title' => 'Is the "she\'ll be right" attitude hurting Australian dating?', 'content' => '<p>Sometimes Australians are accused of being too casual about relationships. Do you agree? How do you navigate this?</p>'],
                ],
            ],
            [
                'flag'         => '🇮🇳',
                'name'         => 'India Dating & Modern Love',
                'country_code' => 'IN',
                'description'  => 'Connect with Indians navigating modern dating, arranged marriage vs love marriages and more.',
                'color'        => '#FF9933',
                'topics'       => [
                    ['title' => 'Arranged vs love marriage in modern India 2026', 'content' => '<p>The lines are blurring. Many couples now meet on apps but involve their families in the decision. What\'s your take?</p>'],
                    ['title' => 'Dating in Indian metros — Mumbai, Delhi, Bangalore', 'content' => '<p>Each city has its own dating culture. Tech workers in Bangalore, finance in Mumbai — how does your city shape your dating life?</p>'],
                ],
            ],
            [
                'flag'         => '🇿🇦',
                'name'         => 'South Africa Dating Forum',
                'country_code' => 'ZA',
                'description'  => 'South Africans connect, share stories and discuss relationships across cultures.',
                'color'        => '#007A4D',
                'topics'       => [
                    ['title' => 'Dating across South Africa\'s cultures', 'content' => '<p>SA is beautifully diverse — with 11 official languages and many cultures. How do you navigate cross-cultural relationships?</p>'],
                    ['title' => 'Cape Town vs Joburg dating scene', 'content' => '<p>Different vibes, different people. Where do you find the dating scene better and why?</p>'],
                ],
            ],
            [
                'flag'         => '🇩🇪',
                'name'         => 'Germany Dating & Culture',
                'country_code' => 'DE',
                'description'  => 'German and expat members in Germany discuss dating culture and relationships.',
                'color'        => '#FFCE00',
                'topics'       => [
                    ['title' => 'Is German dating culture really as serious as everyone says?', 'content' => '<p>Germans have a reputation for being direct and not playing games. In your experience, is this true in the dating world?</p>'],
                ],
            ],
            [
                'flag'         => '🌍',
                'name'         => 'African Diaspora Dating',
                'country_code' => null,
                'description'  => 'For Africans living abroad — navigating diaspora identity, dating and culture.',
                'color'        => '#CC0000',
                'topics'       => [
                    ['title' => 'Dating as an African abroad — unique challenges', 'content' => '<p>Living in Europe, North America or Asia as an African comes with unique dating dynamics. Let\'s talk about it honestly.</p>'],
                    ['title' => 'Should diaspora Africans only date other diaspora Africans?', 'content' => '<p>Shared experiences matter, but so does openness. What has worked for you dating-wise while living abroad?</p>'],
                ],
            ],
        ];

        $order = 50; // Start after existing categories
        foreach ($countries as $countryData) {
            $name = $countryData['flag'] . ' ' . $countryData['name'];
            $slug = Str::slug($countryData['name']);

            $category = ForumCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'              => $name,
                    'description'       => $countryData['description'],
                    'icon'              => $countryData['flag'],
                    'color'             => $countryData['color'],
                    'order'             => $order++,
                    'is_active'         => true,
                    'requires_verified' => false,
                    'country_code'      => $countryData['country_code'] ?? null,
                ]
            );

            foreach ($countryData['topics'] as $topicData) {
                $topicSlug = Str::slug($topicData['title']) . '-' . substr(md5($topicData['title']), 0, 6);

                ForumTopic::firstOrCreate(
                    ['slug' => $topicSlug],
                    [
                        'category_id'   => $category->id,
                        'user_id'       => $adminUser->id,
                        'title'         => $topicData['title'],
                        'slug'          => $topicSlug,
                        'content'       => $topicData['content'],
                        'tags'          => [],
                        'last_reply_at' => now()->subDays(rand(1, 30)),
                        'views_count'   => rand(10, 200),
                    ]
                );
            }

            $this->command->info("Seeded country forum: {$name}");
        }
    }
}
