<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds 30 Nigerian users (15 male, 15 female) spread across major cities.
 */
class NigeriaSeeder extends Seeder
{
    private array $maleNames = [
        'Emeka', 'Chidi', 'Tunde', 'Seun', 'Kola', 'Bayo', 'Yemi', 'Ifeanyi',
        'Obinna', 'Nnamdi', 'Femi', 'Gbenga', 'Lanre', 'Dapo', 'Uche',
    ];

    private array $femaleNames = [
        'Amara', 'Ngozi', 'Chioma', 'Funke', 'Bisi', 'Shade', 'Nneka',
        'Adaeze', 'Kemi', 'Yetunde', 'Bukola', 'Adaora', 'Blessing', 'Chiamaka', 'Titi',
    ];

    private array $cities = [
        ['Lagos',          'Lagos State',      6.5244,   3.3792],
        ['Abuja',          'FCT',              9.0579,   7.4951],
        ['Kano',           'Kano State',      12.0000,   8.5167],
        ['Ibadan',         'Oyo State',        7.3776,   3.9470],
        ['Port Harcourt',  'Rivers State',     4.8156,   7.0498],
        ['Benin City',     'Edo State',        6.3354,   5.6270],
        ['Enugu',          'Enugu State',      6.4527,   7.5130],
        ['Kaduna',         'Kaduna State',    10.5105,   7.4165],
        ['Aba',            'Abia State',       5.1066,   7.3667],
        ['Warri',          'Delta State',      5.5167,   5.7500],
        ['Calabar',        'Cross River',      4.9500,   8.3250],
        ['Onitsha',        'Anambra State',    6.1465,   6.7741],
        ['Owerri',         'Imo State',        5.4836,   7.0333],
        ['Uyo',            'Akwa Ibom',        5.0377,   7.9128],
        ['Ilorin',         'Kwara State',      8.4966,   4.5421],
    ];

    private array $taglines = [
        'Looking for my forever person ❤️',
        'Living, laughing, loving life in Naija',
        'Ambitious and down to earth',
        'Believe in God, love people, enjoy life',
        'Simple girl/guy with big dreams',
        'Food lover | Music head | Good vibes only',
        'Faith. Family. Fun.',
        'Looking for something real, not just a chat',
        'Naija to the bone 🦅',
        'Work hard, stay humble, find love',
        'Life is too short for boring conversations',
        'Adventure seeker with a warm heart',
        'Just a Lagos/Abuja/PH girl/boy living life',
        'God first, everything else falls in place',
        'Straightforward and ready for something meaningful',
    ];

    private array $bios = [
        'I am a simple Nigerian who values family, God, and genuine connection. Let\'s see where this goes.',
        'Lagos life has taught me to be resilient and cheerful. I love good food, Afrobeats, and honest people.',
        'Looking for someone to share the journey of life with. I am hardworking, caring, and full of positive energy.',
        'I believe in building something real. No time for games — let\'s talk and see if we vibe.',
        'I enjoy good company, long conversations, and exploring new restaurants. I am loyal and God-fearing.',
        'Whether it\'s suya nights or quiet evenings at home, I appreciate the simple pleasures. DM me!',
        'Born and raised in Nigeria, now building my future one step at a time. Looking for my equally driven partner.',
        'I am outgoing, funny, and easy to talk to. I love travelling across Nigeria and meeting new people.',
        'I put God first in everything I do. I am looking for someone with similar values and a good heart.',
        'Nollywood fan, jollof rice enthusiast, and someone who believes love is worth the effort.',
    ];

    public function run(): void
    {
        $interests = Interest::all();
        $created   = 0;

        foreach ($this->maleNames as $i => $name) {
            $city = $this->cities[$i % count($this->cities)];
            if ($this->seedUser($name, 'male', $city, $i, $interests)) {
                $created++;
            }
        }

        foreach ($this->femaleNames as $i => $name) {
            $city = $this->cities[($i + 1) % count($this->cities)]; // offset so cities vary
            if ($this->seedUser($name, 'female', $city, $i + 50, $interests)) {
                $created++;
            }
        }

        $this->command->info("✅ NigeriaSeeder: {$created} new Nigerian users created.");
    }

    private function seedUser(string $firstName, string $gender, array $city, int $idx, $interests): bool
    {
        [$cityName, $state, $lat, $lng] = $city;

        $suffix   = $gender === 'male' ? 'm' : 'f';
        $slug     = strtolower(str_replace(' ', '', $firstName));
        $username = "ng_{$slug}_{$suffix}_{$idx}";
        $email    = "{$username}@test.heartsconnect.com";

        if (User::where('email', $email)->orWhere('username', $username)->exists()) {
            return false;
        }

        $dob     = now()->subYears(fake()->numberBetween(21, 38))
                        ->subDays(fake()->numberBetween(0, 364))
                        ->format('Y-m-d');
        $seeking = $gender === 'male'
            ? fake()->randomElement(['female', 'everyone'])
            : fake()->randomElement(['male', 'everyone']);

        $user = User::create([
            'name'              => $firstName . ' ' . fake()->randomElement(['Okonkwo', 'Adeyemi', 'Nwosu', 'Balogun', 'Eze', 'Okafor', 'Adeleke', 'Obi', 'Musa', 'Ibrahim', 'Chukwu', 'Adesanya', 'Onyeka', 'Ogundele', 'Nzeogwu']),
            'username'          => $username,
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => Hash::make('TestPass@2026'),
            'gender'            => $gender,
            'seeking'           => $seeking,
            'date_of_birth'     => $dob,
            'profile_complete'  => true,
            'onboarding_step'   => 5,
            'last_active_at'    => now()->subHours(fake()->numberBetween(0, 120)),
        ]);

        $user->assignRole('user');

        $jitterLat = $lat + fake()->randomFloat(4, -0.08, 0.08);
        $jitterLng = $lng + fake()->randomFloat(4, -0.08, 0.08);

        $profile = Profile::create([
            'user_id'           => $user->id,
            'headline'          => fake()->randomElement($this->taglines),
            'bio'               => fake()->randomElement($this->bios),
            'city'              => $cityName,
            'state'             => $state,
            'country'           => 'Nigeria',
            'latitude'          => $jitterLat,
            'longitude'         => $jitterLng,
            'height_cm'         => $gender === 'male'
                                    ? fake()->numberBetween(168, 195)
                                    : fake()->numberBetween(155, 178),
            'body_type'         => fake()->randomElement(['slim', 'athletic', 'average', 'curvy']),
            'ethnicity'         => 'black',
            'education'         => fake()->randomElement(['bachelors', 'masters', 'some_college', 'phd', 'high_school']),
            'occupation'        => fake()->jobTitle(),
            'religion'          => fake()->randomElement(['christian', 'muslim', 'spiritual', 'none']),
            'smoking'           => fake()->randomElement(['never', 'occasionally']),
            'drinking'          => fake()->randomElement(['never', 'socially']),
            'has_children'      => fake()->boolean(20),
            'wants_children'    => fake()->randomElement(['yes', 'open', 'not_sure']),
            'relationship_goal' => fake()->randomElement(['serious', 'marriage', 'friendship', 'casual']),
            'location_updates_count' => 0,
        ]);

        if ($interests->isNotEmpty()) {
            $profile->interests()->sync(
                $interests->random(min(fake()->numberBetween(3, 8), $interests->count()))->pluck('id')
            );
        }

        UserPreference::create([
            'user_id'          => $user->id,
            'seeking_gender'   => $seeking,
            'min_age'          => fake()->numberBetween(18, 24),
            'max_age'          => fake()->numberBetween(30, 50),
            'max_distance_km'  => null,
            'show_online_only' => false,
            'body_types'       => null,
        ]);

        return true;
    }
}
