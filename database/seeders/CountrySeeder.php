<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one male and one female test user for each major country,
 * with realistic city names and GPS coordinates.
 */
class CountrySeeder extends Seeder
{
    /** [country, city, state/region, latitude, longitude, male_first_name, female_first_name] */
    private array $countries = [
        ['United States',        'New York',       'New York',      40.7128,   -74.0060,  'James',     'Emily'],
        ['United Kingdom',       'London',         'England',       51.5074,    -0.1278,  'Oliver',    'Sophie'],
        ['Canada',               'Toronto',        'Ontario',       43.6510,   -79.3470,  'Liam',      'Emma'],
        ['Australia',            'Sydney',         'New South Wales',-33.8688, 151.2093,  'Noah',      'Olivia'],
        ['Germany',              'Berlin',         'Berlin',        52.5200,    13.4050,  'Lukas',     'Hannah'],
        ['France',               'Paris',          'Île-de-France', 48.8566,     2.3522,  'Lucas',     'Camille'],
        ['India',                'Mumbai',         'Maharashtra',   19.0760,    72.8777,  'Arjun',     'Priya'],
        ['Nigeria',              'Lagos',          'Lagos State',    6.5244,     3.3792,  'Emeka',     'Amara'],
        ['Brazil',               'São Paulo',      'São Paulo',     -23.5505,  -46.6333,  'Gabriel',   'Isabela'],
        ['Mexico',               'Mexico City',    'CDMX',          19.4326,   -99.1332,  'Mateo',     'Valentina'],
        ['Japan',                'Tokyo',          'Tokyo',         35.6762,   139.6503,  'Kenji',     'Yuki'],
        ['South Korea',          'Seoul',          'Seoul',         37.5665,   126.9780,  'Minho',     'Jiyeon'],
        ['Spain',                'Madrid',         'Madrid',        40.4168,    -3.7038,  'Carlos',    'María'],
        ['Italy',                'Rome',           'Lazio',         41.9028,    12.4964,  'Marco',     'Giulia'],
        ['Netherlands',          'Amsterdam',      'North Holland', 52.3676,     4.9041,  'Daan',      'Sanne'],
        ['Sweden',               'Stockholm',      'Stockholm',     59.3293,    18.0686,  'Erik',      'Maja'],
        ['South Africa',         'Cape Town',      'Western Cape',  -33.9249,   18.4241,  'Sipho',     'Nomsa'],
        ['Kenya',                'Nairobi',        'Nairobi',       -1.2921,    36.8219,  'Brian',     'Grace'],
        ['Egypt',                'Cairo',          'Cairo',         30.0444,    31.2357,  'Ahmed',     'Fatima'],
        ['United Arab Emirates', 'Dubai',          'Dubai',         25.2048,    55.2708,  'Omar',      'Layla'],
        ['Saudi Arabia',         'Riyadh',         'Riyadh',        24.7136,    46.6753,  'Khalid',    'Nora'],
        ['Pakistan',             'Lahore',         'Punjab',        31.5497,    74.3436,  'Ali',       'Ayesha'],
        ['Bangladesh',           'Dhaka',          'Dhaka',         23.8103,    90.4125,  'Rahim',     'Nadia'],
        ['Philippines',          'Manila',         'Metro Manila',  14.5995,   120.9842,  'Juan',      'Maria'],
        ['Indonesia',            'Jakarta',        'DKI Jakarta',   -6.2088,   106.8456,  'Budi',      'Sari'],
        ['Vietnam',              'Ho Chi Minh',    'Ho Chi Minh',   10.8231,   106.6297,  'Minh',      'Linh'],
        ['Thailand',             'Bangkok',        'Bangkok',       13.7563,   100.5018,  'Chai',      'Nang'],
        ['Argentina',            'Buenos Aires',   'Buenos Aires',  -34.6037,  -58.3816,  'Nicolás',   'Sofía'],
        ['Colombia',             'Bogotá',         'Cundinamarca',   4.7110,   -74.0721,  'Andrés',    'Daniela'],
        ['Chile',                'Santiago',       'Santiago',      -33.4489,  -70.6693,  'Diego',     'Camila'],
        ['Peru',                 'Lima',           'Lima',          -12.0464,  -77.0428,  'Luis',      'Ana'],
        ['Ghana',                'Accra',          'Greater Accra',  5.6037,    -0.1870,  'Kwame',     'Abena'],
        ['Tanzania',             'Dar es Salaam',  'Dar es Salaam', -6.7924,    39.2083,  'Joseph',    'Amina'],
        ['Ethiopia',             'Addis Ababa',    'Addis Ababa',    9.0320,    38.7469,  'Yonas',     'Tigist'],
        ['Morocco',              'Casablanca',     'Casablanca',    33.5731,    -7.5898,  'Youssef',   'Salma'],
        ['Turkey',               'Istanbul',       'Istanbul',      41.0082,    28.9784,  'Mehmet',    'Elif'],
        ['Poland',               'Warsaw',         'Masovian',      52.2297,    21.0122,  'Piotr',     'Anna'],
        ['Ukraine',              'Kyiv',           'Kyiv Oblast',   50.4501,    30.5234,  'Oleksiy',   'Olena'],
        ['Romania',              'Bucharest',      'Bucharest',     44.4268,    26.1025,  'Andrei',    'Maria'],
        ['Czech Republic',       'Prague',         'Prague',        50.0755,    14.4378,  'Tomáš',     'Tereza'],
        ['Portugal',             'Lisbon',         'Lisbon',        38.7169,    -9.1399,  'João',      'Inês'],
        ['Greece',               'Athens',         'Attica',        37.9838,    23.7275,  'Nikos',     'Elena'],
        ['Hungary',              'Budapest',       'Budapest',      47.4979,    19.0402,  'Péter',     'Kata'],
        ['Finland',              'Helsinki',       'Uusimaa',       60.1699,    24.9384,  'Mikko',     'Aino'],
        ['Norway',               'Oslo',           'Oslo',          59.9139,    10.7522,  'Lars',      'Ingrid'],
        ['Denmark',              'Copenhagen',     'Capital Region',55.6761,    12.5683,  'Søren',     'Astrid'],
        ['New Zealand',          'Auckland',       'Auckland',      -36.8509,  174.7645,  'Jack',      'Charlotte'],
        ['Singapore',            'Singapore',      'Singapore',      1.3521,   103.8198,  'Wei',       'Hui'],
        ['Malaysia',             'Kuala Lumpur',   'WP KL',          3.1390,   101.6869,  'Amir',      'Nurul'],
        ['Russia',               'Moscow',         'Moscow',        55.7558,    37.6173,  'Dmitri',    'Natasha'],
        ['China',                'Shanghai',       'Shanghai',      31.2304,   121.4737,  'Wei',       'Xiu'],
        ['Ireland',              'Dublin',         'Leinster',      53.3498,    -6.2603,  'Sean',      'Aoife'],
        ['Belgium',              'Brussels',       'Brussels',      50.8503,     4.3517,  'Thomas',    'Julie'],
        ['Austria',              'Vienna',         'Vienna',        48.2082,    16.3738,  'Stefan',    'Julia'],
        ['Switzerland',          'Zurich',         'Zurich',        47.3769,     8.5417,  'Adrian',    'Laura'],
        ['Israel',               'Tel Aviv',       'Tel Aviv',      32.0853,    34.7818,  'Avi',       'Noa'],
    ];

    private array $taglines = [
        'Looking for my adventure partner',
        'Coffee addict seeking companion',
        'Life is short, eat the cake',
        'Dog lover & weekend hiker',
        'Sarcasm is my love language',
        'Amateur chef, professional eater',
        'Bookworm with a wild side',
        'Gym rat who loves pizza too',
        'Searching for my partner in crime',
        'Just here for a good time',
        'Travel. Food. Connection.',
        'Making memories one day at a time',
    ];

    private array $bios = [
        'I love exploring new places and trying different cuisines. Looking for someone who shares my curiosity about the world.',
        'Work hard, play harder. I value honesty, humor, and genuine connection above all else.',
        'I spend my weekends hiking, cooking elaborate meals, and watching way too many documentaries.',
        'Optimist by nature, realist by experience. I believe in long walks, great conversations, and good coffee.',
        'Spontaneous adventures excite me. Building something meaningful with the right person excites me even more.',
        'I bring the same energy to a lazy Sunday morning as I do to a night out. Balance is everything.',
    ];

    public function run(): void
    {
        $interests    = Interest::all();

        foreach ($this->countries as $idx => $row) {
            [$country, $city, $state, $lat, $lng, $maleName, $femaleName] = $row;

            $this->createUser($idx, $country, $city, $state, $lat, $lng, $maleName,   'male',   $interests);
            $this->createUser($idx, $country, $city, $state, $lat, $lng, $femaleName, 'female', $interests);
        }

        $this->command->info('✅ CountrySeeder: created 2 users × ' . count($this->countries) . ' countries = ' . (count($this->countries) * 2) . ' test users.');
    }

    private function createUser(
        int $idx,
        string $country,
        string $city,
        string $state,
        float $lat,
        float $lng,
        string $firstName,
        string $gender,
        $interests
    ): void {
        $suffix   = $gender === 'male' ? 'm' : 'f';
        $slug     = strtolower(preg_replace('/[^a-z0-9]/i', '', $country));
        $username = "{$slug}_{$suffix}_{$idx}";
        $email    = "{$username}@test.heartsconnect.com";

        // Skip if this test user already exists
        if (User::where('email', $email)->exists()) {
            return;
        }

        $dob = now()->subYears(fake()->numberBetween(22, 42))
                    ->subDays(fake()->numberBetween(0, 364))
                    ->format('Y-m-d');

        $seeking = $gender === 'male' ? fake()->randomElement(['female', 'everyone']) : fake()->randomElement(['male', 'everyone']);

        $user = User::create([
            'name'              => $firstName . ' ' . strtoupper(substr($country, 0, 3)),
            'username'          => $username,
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => Hash::make('TestPass@2026'),
            'gender'            => $gender,
            'seeking'           => $seeking,
            'date_of_birth'     => $dob,
            'profile_complete'  => true,
            'onboarding_step'   => 5,
            'last_active_at'    => now()->subHours(fake()->numberBetween(0, 72)),
        ]);

        $user->assignRole('user');

        // Small coordinate jitter so users aren't stacked on top of each other
        $jitterLat = $lat + fake()->randomFloat(4, -0.05, 0.05);
        $jitterLng = $lng + fake()->randomFloat(4, -0.05, 0.05);

        $profile = Profile::create([
            'user_id'           => $user->id,
            'headline'          => fake()->randomElement($this->taglines),
            'bio'               => fake()->randomElement($this->bios),
            'city'              => $city,
            'state'             => $state,
            'country'           => $country,
            'latitude'          => $jitterLat,
            'longitude'         => $jitterLng,
            'height_cm'         => $gender === 'male'
                                    ? fake()->numberBetween(168, 195)
                                    : fake()->numberBetween(155, 178),
            'body_type'         => fake()->randomElement(['slim', 'athletic', 'average', 'curvy']),
            'ethnicity'         => fake()->randomElement(['white', 'black', 'hispanic', 'asian', 'mixed', 'other']),
            'education'         => fake()->randomElement(['bachelors', 'masters', 'some_college', 'phd']),
            'occupation'        => fake()->jobTitle(),
            'religion'          => fake()->randomElement(['none', 'christian', 'muslim', 'hindu', 'buddhist', 'spiritual', 'other']),
            'smoking'           => fake()->randomElement(['never', 'occasionally']),
            'drinking'          => fake()->randomElement(['never', 'socially']),
            'has_children'      => fake()->boolean(25),
            'wants_children'    => fake()->randomElement(['yes', 'no', 'open', 'not_sure']),
            'relationship_goal' => fake()->randomElement(['casual', 'serious', 'friendship', 'marriage']),
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
            'min_age'          => fake()->numberBetween(18, 25),
            'max_age'          => fake()->numberBetween(30, 55),
            'max_distance_km'  => null,   // no distance limit — visible across the whole site
            'show_online_only' => false,
            'body_types'       => null,
        ]);
    }
}
