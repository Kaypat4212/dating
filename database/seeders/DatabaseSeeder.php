<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed interests
        $this->call(InterestSeeder::class);

        // 2. Seed crypto wallets
        $this->call(CryptoWalletSeeder::class);

        // Seed email templates
        $this->call(EmailTemplateSeeder::class);

        // 3. Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@heartsconnect.com'],
            [
                'name'             => 'Admin User',
                'username'         => 'admin',
                'email_verified_at'=> now(),
                'password'         => Hash::make('Admin@2026'),
                'gender'           => 'male',
                'seeking'          => 'female',
                'date_of_birth'    => '1985-01-01',
                'profile_complete' => true,
                'onboarding_step'  => 5,
                'last_active_at'   => now(),
            ]
        );
        // Ensure admin role exists and is assigned
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin',      'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user',       'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'moderator',  'guard_name' => 'web']);
        $admin->assignRole('admin');

        // Create admin profile
        Profile::firstOrCreate(['user_id' => $admin->id], [
            'headline'          => 'Site Administrator',
            'bio'               => 'HeartsConnect admin account.',
            'city'              => 'New York',
            'state'             => 'NY',
            'country'           => 'US',
            'latitude'          => 40.7128,
            'longitude'         => -74.0060,
            'relationship_goal' => 'serious',
        ]);

        // 4. Create a demo regular user
        $demo = User::firstOrCreate(
            ['email' => 'demo@heartsconnect.com'],
            [
                'name'             => 'Demo User',
                'username'         => 'demouser',
                'email_verified_at'=> now(),
                'password'         => Hash::make('password'),
                'gender'           => 'female',
                'seeking'          => 'male',
                'date_of_birth'    => '1995-06-15',
                'profile_complete' => true,
                'onboarding_step'  => 5,
                'last_active_at'   => now(),
            ]
        );
        $demo->assignRole('user');
        Profile::firstOrCreate(['user_id' => $demo->id], [
            'headline'   => 'Looking for something real ❤️',
            'bio'        => 'Coffee lover, bookworm, and weekend adventurer. I believe in spontaneous road trips and home-cooked meals.',
            'city'       => 'Brooklyn',
            'state'      => 'NY',
            'country'    => 'US',
            'latitude'   => 40.6782,
            'longitude'  => -73.9442,
            'height_cm'  => 165,
            'body_type'  => 'athletic',
            'education'  => 'bachelors',
            'occupation' => 'Graphic Designer',
            'smoking'    => 'never',
            'drinking'   => 'socially',
            'wants_children'    => 'yes',
            'relationship_goal' => 'serious',
        ]);

        // 5. Create 50 fake users with profiles
        $interests = Interest::all();

        User::factory(50)->create()->each(function (User $user) use ($interests) {
            $user->assignRole('user');

            $profile = Profile::create(
                array_merge(
                    (new \Database\Factories\ProfileFactory)->definition(),
                    ['user_id' => $user->id]
                )
            );

            // Assign 3–8 random interests
            if ($interests->isNotEmpty()) {
                $profile->interests()->sync(
                    $interests->random(min(rand(3, 8), $interests->count()))->pluck('id')
                );
            }
        });

        // 6. Seed UserPreferences for ALL users (including admin, demo and the 50 fake ones)
        $this->seedPreferences();

        // 7. Seed mutual likes so that we have real matches to test with
        $this->seedLikesAndMatches();

        // 8. Seed profile photos (downloads portrait placeholders from randomuser.me)
        $this->call(PhotoSeeder::class);

        // 9. Seed one male + one female test user per country
        $this->call(CountrySeeder::class);

        $this->command->info('✅ Database seeded: admin, demo user, 50 fake users, interests, preferences, matches, photos, crypto wallets.');
    }

    // ── Seed UserPreferences ─────────────────────────────────────────────────

    private function seedPreferences(): void
    {
        $users = User::with('preferences')->get();

        foreach ($users as $user) {
            if ($user->preferences) {
                continue; // already has preferences
            }

            // Mirror the user's own seeking field as the preference gender filter
            $seekingGender = $user->seeking ?? 'everyone';

            UserPreference::create([
                'user_id'         => $user->id,
                'seeking_gender'  => $seekingGender,
                'min_age'         => rand(18, 25),
                'max_age'         => rand(28, 65),
                'max_distance_km' => fake()->randomElement([25, 50, 100, 200, 500, 9999]),
                'show_online_only'=> false,
                'body_types'      => null,
            ]);
        }

        $this->command->info('✅ UserPreferences seeded.');
    }

    // ── Seed Mutual Likes → Matches → Conversations ──────────────────────────

    private function seedLikesAndMatches(): void
    {
        // Take 40 complete users (excluding admin) and pair them up
        $pool = User::where('profile_complete', true)
            ->whereNotIn('email', ['admin@heartsconnect.com'])
            ->inRandomOrder()
            ->take(40)
            ->get();

        $matchCount = 0;

        // Pair consecutive users as mutual-like couples
        for ($i = 0; $i + 1 < $pool->count(); $i += 2) {
            $a = $pool[$i];
            $b = $pool[$i + 1];

            // Insert both directions of the like
            Like::firstOrCreate(['sender_id' => $a->id, 'receiver_id' => $b->id]);
            Like::firstOrCreate(['sender_id' => $b->id, 'receiver_id' => $a->id]);

            // Enforce user1_id < user2_id convention
            [$u1, $u2] = $a->id < $b->id ? [$a->id, $b->id] : [$b->id, $a->id];

            $match = UserMatch::firstOrCreate(
                ['user1_id' => $u1, 'user2_id' => $u2],
                ['matched_at' => now()->subDays(rand(0, 30)), 'is_active' => true]
            );

            if ($match->wasRecentlyCreated) {
                Conversation::firstOrCreate(['match_id' => $match->id]);
                $matchCount++;
            }
        }

        $this->command->info("✅ {$matchCount} matches (with conversations) seeded.");
    }
}

