<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    public function definition(): array
    {
        // Spread across major cities worldwide with realistic lat/lng
        // Format: [city, state/region, country, latitude, longitude]
        $cities = [
            // United States
            ['New York',        'NY',               'United States',        40.7128,   -74.0060],
            ['Los Angeles',     'CA',               'United States',        34.0522,  -118.2437],
            ['Chicago',         'IL',               'United States',        41.8781,   -87.6298],
            ['Houston',         'TX',               'United States',        29.7604,   -95.3698],
            ['Miami',           'FL',               'United States',        25.7617,   -80.1918],
            ['Seattle',         'WA',               'United States',        47.6062,  -122.3321],
            ['Austin',          'TX',               'United States',        30.2672,   -97.7431],
            ['Denver',          'CO',               'United States',        39.7392,  -104.9903],
            ['Atlanta',         'GA',               'United States',        33.7490,   -84.3880],
            ['Boston',          'MA',               'United States',        42.3601,   -71.0589],
            // United Kingdom
            ['London',          'England',          'United Kingdom',       51.5074,    -0.1278],
            ['Manchester',      'England',          'United Kingdom',       53.4808,    -2.2426],
            ['Birmingham',      'England',          'United Kingdom',       52.4862,    -1.8904],
            ['Edinburgh',       'Scotland',         'United Kingdom',       55.9533,    -3.1883],
            // Europe
            ['Paris',           'Île-de-France',    'France',               48.8566,     2.3522],
            ['Berlin',          'Berlin',           'Germany',              52.5200,    13.4050],
            ['Madrid',          'Madrid',           'Spain',                40.4168,    -3.7038],
            ['Rome',            'Lazio',            'Italy',                41.9028,    12.4964],
            ['Amsterdam',       'North Holland',    'Netherlands',          52.3676,     4.9041],
            ['Brussels',        'Brussels',         'Belgium',              50.8503,     4.3517],
            ['Vienna',          'Vienna',           'Austria',              48.2082,    16.3738],
            ['Stockholm',       'Stockholm',        'Sweden',               59.3293,    18.0686],
            ['Warsaw',          'Masovian',         'Poland',               52.2297,    21.0122],
            ['Lisbon',          'Lisbon',           'Portugal',             38.7169,    -9.1399],
            ['Prague',          'Prague',           'Czech Republic',       50.0755,    14.4378],
            ['Athens',          'Attica',           'Greece',               37.9838,    23.7275],
            ['Budapest',        'Budapest',         'Hungary',              47.4979,    19.0402],
            ['Oslo',            'Oslo',             'Norway',               59.9139,    10.7522],
            ['Copenhagen',      'Capital Region',   'Denmark',              55.6761,    12.5683],
            ['Helsinki',        'Uusimaa',          'Finland',              60.1699,    24.9384],
            ['Zurich',          'Zurich',           'Switzerland',          47.3769,     8.5417],
            // North America & Oceania
            ['Toronto',         'Ontario',          'Canada',               43.6510,   -79.3470],
            ['Vancouver',       'British Columbia', 'Canada',               49.2827,  -123.1207],
            ['Mexico City',     'CDMX',             'Mexico',               19.4326,   -99.1332],
            ['Sydney',          'New South Wales',  'Australia',           -33.8688,   151.2093],
            ['Melbourne',       'Victoria',         'Australia',           -37.8136,   144.9631],
            ['Auckland',        'Auckland',         'New Zealand',         -36.8509,   174.7645],
            // Asia
            ['Tokyo',           'Tokyo',            'Japan',                35.6762,   139.6503],
            ['Seoul',           'Seoul',            'South Korea',          37.5665,   126.9780],
            ['Shanghai',        'Shanghai',         'China',                31.2304,   121.4737],
            ['Mumbai',          'Maharashtra',      'India',                19.0760,    72.8777],
            ['Delhi',           'Delhi',            'India',                28.7041,    77.1025],
            ['Bangalore',       'Karnataka',        'India',                12.9716,    77.5946],
            ['Bangkok',         'Bangkok',          'Thailand',             13.7563,   100.5018],
            ['Manila',          'Metro Manila',     'Philippines',          14.5995,   120.9842],
            ['Jakarta',         'DKI Jakarta',      'Indonesia',            -6.2088,   106.8456],
            ['Kuala Lumpur',    'WP KL',            'Malaysia',              3.1390,   101.6869],
            ['Singapore',       'Singapore',        'Singapore',             1.3521,   103.8198],
            ['Ho Chi Minh',     'Ho Chi Minh',      'Vietnam',              10.8231,   106.6297],
            ['Dhaka',           'Dhaka',            'Bangladesh',           23.8103,    90.4125],
            ['Lahore',          'Punjab',           'Pakistan',             31.5497,    74.3436],
            ['Dubai',           'Dubai',            'United Arab Emirates', 25.2048,    55.2708],
            ['Istanbul',        'Istanbul',         'Turkey',               41.0082,    28.9784],
            // Africa
            ['Lagos',           'Lagos State',      'Nigeria',               6.5244,     3.3792],
            ['Nairobi',         'Nairobi',          'Kenya',                -1.2921,    36.8219],
            ['Cairo',           'Cairo',            'Egypt',                30.0444,    31.2357],
            ['Cape Town',       'Western Cape',     'South Africa',        -33.9249,    18.4241],
            ['Casablanca',      'Casablanca',       'Morocco',              33.5731,    -7.5898],
            ['Accra',           'Greater Accra',    'Ghana',                 5.6037,    -0.1870],
            ['Addis Ababa',     'Addis Ababa',      'Ethiopia',              9.0320,    38.7469],
            // Latin America
            ['São Paulo',       'São Paulo',        'Brazil',              -23.5505,   -46.6333],
            ['Buenos Aires',    'Buenos Aires',     'Argentina',           -34.6037,   -58.3816],
            ['Bogotá',          'Cundinamarca',     'Colombia',              4.7110,   -74.0721],
            ['Santiago',        'Santiago',         'Chile',               -33.4489,   -70.6693],
            ['Lima',            'Lima',             'Peru',                -12.0464,   -77.0428],
        ];

        [$city, $state, $country, $lat, $lng] = fake()->randomElement($cities);
        // Add small jitter for uniqueness
        $lat += fake()->randomFloat(4, -0.2, 0.2);
        $lng += fake()->randomFloat(4, -0.2, 0.2);

        $taglines = [
            'Looking for my adventure partner', 'Coffee addict seeking companion',
            'Life is short, eat the cake', 'Dog lover & weekend hiker',
            'Sarcasm is my love language', 'Amateur chef, professional eater',
            'Netflix and actual chill', 'Gym rat who loves pizza too',
            'Searching for my partner in crime', 'Bookworm with a wild side',
        ];

        $heights = ['5\'4"', '5\'5"', '5\'6"', '5\'7"', '5\'8"', '5\'9"', '5\'10"', '5\'11"', '6\'0"', '6\'1"', '6\'2"'];

        return [
            'user_id'           => null, // set by seeder
            'headline'          => fake()->randomElement($taglines),
            'bio'               => fake()->paragraphs(fake()->numberBetween(1, 3), true),
            'city'              => $city,
            'state'             => $state,
            'country'           => $country,
            'latitude'          => $lat,
            'longitude'         => $lng,
            'height_cm'         => fake()->numberBetween(155, 195),
            'body_type'         => fake()->randomElement(['slim', 'athletic', 'average', 'curvy', 'large']),
            'ethnicity'         => fake()->randomElement(['white', 'black', 'hispanic', 'asian', 'mixed', 'other']),
            'education'         => fake()->randomElement(['high_school', 'some_college', 'bachelors', 'masters', 'phd']),
            'occupation'        => fake()->jobTitle(),
            'religion'          => fake()->randomElement(['none', 'christian', 'catholic', 'jewish', 'muslim', 'hindu', 'buddhist', 'spiritual', 'other']),
            'smoking'           => fake()->randomElement(['never', 'occasionally', 'regularly']),
            'drinking'          => fake()->randomElement(['never', 'socially', 'regularly']),
            'has_children'      => fake()->boolean(30),
            'wants_children'    => fake()->randomElement(['yes', 'no', 'open', 'not_sure']),
            'relationship_goal' => fake()->randomElement(['casual', 'serious', 'friendship', 'marriage']),
        ];
    }
}
