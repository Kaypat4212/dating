<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Canonical list: full country name => ISO 2-letter code (for dropdown).
     * The key is what gets stored in the database.
     */
    public static function list(): array
    {
        return [
            'Afghanistan'          => 'AF',
            'Albania'              => 'AL',
            'Algeria'              => 'DZ',
            'Argentina'            => 'AR',
            'Australia'            => 'AU',
            'Austria'              => 'AT',
            'Bangladesh'           => 'BD',
            'Belgium'              => 'BE',
            'Brazil'               => 'BR',
            'Canada'               => 'CA',
            'Chile'                => 'CL',
            'China'                => 'CN',
            'Colombia'             => 'CO',
            'Croatia'              => 'HR',
            'Czech Republic'       => 'CZ',
            'Denmark'              => 'DK',
            'Egypt'                => 'EG',
            'Ethiopia'             => 'ET',
            'Finland'              => 'FI',
            'France'               => 'FR',
            'Germany'              => 'DE',
            'Ghana'                => 'GH',
            'Greece'               => 'GR',
            'Hungary'              => 'HU',
            'India'                => 'IN',
            'Indonesia'            => 'ID',
            'Ireland'              => 'IE',
            'Israel'               => 'IL',
            'Italy'                => 'IT',
            'Japan'                => 'JP',
            'Jordan'               => 'JO',
            'Kenya'                => 'KE',
            'Malaysia'             => 'MY',
            'Mexico'               => 'MX',
            'Morocco'              => 'MA',
            'Myanmar'              => 'MM',
            'Netherlands'          => 'NL',
            'New Zealand'          => 'NZ',
            'Nigeria'              => 'NG',
            'Norway'               => 'NO',
            'Pakistan'             => 'PK',
            'Peru'                 => 'PE',
            'Philippines'          => 'PH',
            'Poland'               => 'PL',
            'Portugal'             => 'PT',
            'Romania'              => 'RO',
            'Russia'               => 'RU',
            'Saudi Arabia'         => 'SA',
            'Senegal'              => 'SN',
            'Singapore'            => 'SG',
            'South Africa'         => 'ZA',
            'South Korea'          => 'KR',
            'Spain'                => 'ES',
            'Sri Lanka'            => 'LK',
            'Sweden'               => 'SE',
            'Switzerland'          => 'CH',
            'Tanzania'             => 'TZ',
            'Thailand'             => 'TH',
            'Turkey'               => 'TR',
            'Uganda'               => 'UG',
            'Ukraine'              => 'UA',
            'United Arab Emirates' => 'AE',
            'United Kingdom'       => 'GB',
            'United States'        => 'US',
            'Vietnam'              => 'VN',
            'Zimbabwe'             => 'ZW',
        ];
    }

    /**
     * Map of lowercase shortcodes/aliases → canonical full name stored in DB.
     */
    private static array $aliases = [
        // United States
        'us'            => 'United States',
        'usa'           => 'United States',
        'u.s.'          => 'United States',
        'u.s.a.'        => 'United States',
        'america'       => 'United States',
        'united states' => 'United States',
        // United Kingdom
        'uk'            => 'United Kingdom',
        'gb'            => 'United Kingdom',
        'u.k.'          => 'United Kingdom',
        'great britain' => 'United Kingdom',
        'britain'       => 'United Kingdom',
        'england'       => 'United Kingdom',
        'scotland'      => 'United Kingdom',
        'wales'         => 'United Kingdom',
        'united kingdom'=> 'United Kingdom',
        // Canada
        'ca'            => 'Canada',
        'can'           => 'Canada',
        'canada'        => 'Canada',
        // Australia
        'au'            => 'Australia',
        'aus'           => 'Australia',
        'australia'     => 'Australia',
        // Germany
        'de'            => 'Germany',
        'deu'           => 'Germany',
        'deutschland'   => 'Germany',
        'germany'       => 'Germany',
        // France
        'fr'            => 'France',
        'fra'           => 'France',
        'france'        => 'France',
        // India
        'in'            => 'India',
        'ind'           => 'India',
        'india'         => 'India',
        // Nigeria
        'ng'            => 'Nigeria',
        'nga'           => 'Nigeria',
        'nigeria'       => 'Nigeria',
        // Brazil
        'br'            => 'Brazil',
        'bra'           => 'Brazil',
        'brasil'        => 'Brazil',
        'brazil'        => 'Brazil',
        // Mexico
        'mx'            => 'Mexico',
        'mex'           => 'Mexico',
        'mexico'        => 'Mexico',
        // Japan
        'jp'            => 'Japan',
        'jpn'           => 'Japan',
        'japan'         => 'Japan',
        // South Korea
        'kr'            => 'South Korea',
        'kor'           => 'South Korea',
        'korea'         => 'South Korea',
        'south korea'   => 'South Korea',
        // Spain
        'es'            => 'Spain',
        'esp'           => 'Spain',
        'spain'         => 'Spain',
        // Italy
        'it'            => 'Italy',
        'ita'           => 'Italy',
        'italy'         => 'Italy',
        // Netherlands
        'nl'            => 'Netherlands',
        'nld'           => 'Netherlands',
        'netherlands'   => 'Netherlands',
        'holland'       => 'Netherlands',
        // Sweden
        'se'            => 'Sweden',
        'swe'           => 'Sweden',
        'sweden'        => 'Sweden',
        // South Africa
        'za'            => 'South Africa',
        'rsa'           => 'South Africa',
        'south africa'  => 'South Africa',
        // Kenya
        'ke'            => 'Kenya',
        'ken'           => 'Kenya',
        'kenya'         => 'Kenya',
        // Egypt
        'eg'            => 'Egypt',
        'egy'           => 'Egypt',
        'egypt'         => 'Egypt',
        // UAE
        'ae'            => 'United Arab Emirates',
        'uae'           => 'United Arab Emirates',
        'united arab emirates' => 'United Arab Emirates',
        // Saudi Arabia
        'sa'            => 'Saudi Arabia',
        'ksa'           => 'Saudi Arabia',
        'saudi'         => 'Saudi Arabia',
        'saudi arabia'  => 'Saudi Arabia',
        // Pakistan
        'pk'            => 'Pakistan',
        'pak'           => 'Pakistan',
        'pakistan'      => 'Pakistan',
        // Bangladesh
        'bd'            => 'Bangladesh',
        'bgd'           => 'Bangladesh',
        'bangladesh'    => 'Bangladesh',
        // Philippines
        'ph'            => 'Philippines',
        'phl'           => 'Philippines',
        'philippines'   => 'Philippines',
        // Indonesia
        'id'            => 'Indonesia',
        'idn'           => 'Indonesia',
        'indonesia'     => 'Indonesia',
        // Vietnam
        'vn'            => 'Vietnam',
        'vnm'           => 'Vietnam',
        'vietnam'       => 'Vietnam',
        'viet nam'      => 'Vietnam',
        // Thailand
        'th'            => 'Thailand',
        'tha'           => 'Thailand',
        'thailand'      => 'Thailand',
        // Argentina
        'ar'            => 'Argentina',
        'arg'           => 'Argentina',
        'argentina'     => 'Argentina',
        // Colombia
        'co'            => 'Colombia',
        'col'           => 'Colombia',
        'colombia'      => 'Colombia',
        // Chile
        'cl'            => 'Chile',
        'chl'           => 'Chile',
        'chile'         => 'Chile',
        // Peru
        'pe'            => 'Peru',
        'per'           => 'Peru',
        'peru'          => 'Peru',
        // Ghana
        'gh'            => 'Ghana',
        'gha'           => 'Ghana',
        'ghana'         => 'Ghana',
        // Tanzania
        'tz'            => 'Tanzania',
        'tza'           => 'Tanzania',
        'tanzania'      => 'Tanzania',
        // Ethiopia
        'et'            => 'Ethiopia',
        'eth'           => 'Ethiopia',
        'ethiopia'      => 'Ethiopia',
        // Morocco
        'ma'            => 'Morocco',
        'mar'           => 'Morocco',
        'morocco'       => 'Morocco',
        // Turkey
        'tr'            => 'Turkey',
        'tur'           => 'Turkey',
        'türkiye'       => 'Turkey',
        'turkiye'       => 'Turkey',
        'turkey'        => 'Turkey',
        // Poland
        'pl'            => 'Poland',
        'pol'           => 'Poland',
        'poland'        => 'Poland',
        // Ukraine
        'ua'            => 'Ukraine',
        'ukr'           => 'Ukraine',
        'ukraine'       => 'Ukraine',
        // Romania
        'ro'            => 'Romania',
        'rou'           => 'Romania',
        'romania'       => 'Romania',
        // Czech Republic
        'cz'            => 'Czech Republic',
        'cze'           => 'Czech Republic',
        'czech'         => 'Czech Republic',
        'czech republic'=> 'Czech Republic',
        'czechia'       => 'Czech Republic',
        // Portugal
        'pt'            => 'Portugal',
        'prt'           => 'Portugal',
        'portugal'      => 'Portugal',
        // Greece
        'gr'            => 'Greece',
        'grc'           => 'Greece',
        'greece'        => 'Greece',
        // Hungary
        'hu'            => 'Hungary',
        'hun'           => 'Hungary',
        'hungary'       => 'Hungary',
        // Finland
        'fi'            => 'Finland',
        'fin'           => 'Finland',
        'finland'       => 'Finland',
        // Norway
        'no'            => 'Norway',
        'nor'           => 'Norway',
        'norway'        => 'Norway',
        // Denmark
        'dk'            => 'Denmark',
        'dnk'           => 'Denmark',
        'denmark'       => 'Denmark',
        // New Zealand
        'nz'            => 'New Zealand',
        'nzl'           => 'New Zealand',
        'new zealand'   => 'New Zealand',
        // Singapore
        'sg'            => 'Singapore',
        'sgp'           => 'Singapore',
        'singapore'     => 'Singapore',
        // Malaysia
        'my'            => 'Malaysia',
        'mys'           => 'Malaysia',
        'malaysia'      => 'Malaysia',
        // Russia
        'ru'            => 'Russia',
        'rus'           => 'Russia',
        'russia'        => 'Russia',
        // China
        'cn'            => 'China',
        'chn'           => 'China',
        'china'         => 'China',
        // Israel
        'il'            => 'Israel',
        'isr'           => 'Israel',
        'israel'        => 'Israel',
        // Ireland
        'ie'            => 'Ireland',
        'irl'           => 'Ireland',
        'ireland'       => 'Ireland',
        // Belgium
        'be'            => 'Belgium',
        'bel'           => 'Belgium',
        'belgium'       => 'Belgium',
        // Austria
        'at'            => 'Austria',
        'aut'           => 'Austria',
        'austria'       => 'Austria',
        // Switzerland
        'ch'            => 'Switzerland',
        'che'           => 'Switzerland',
        'switzerland'   => 'Switzerland',
    ];

    /**
     * Resolve any user input (code, alias, or full name) to the canonical
     * full country name stored in the database.
     *
     * Returns null if the input is blank; returns the input as-is (title-cased)
     * if no mapping is found, so valid full names pass through unchanged.
     */
    public static function resolve(?string $input): ?string
    {
        if ($input === null || trim($input) === '') {
            return null;
        }

        $needle = strtolower(trim($input));

        if (isset(self::$aliases[$needle])) {
            return self::$aliases[$needle];
        }

        // If it already matches a canonical full name (case-insensitive), return it properly cased
        $fullNames = array_keys(self::list());
        foreach ($fullNames as $name) {
            if (strtolower($name) === $needle) {
                return $name;
            }
        }

        // Unknown input — title-case and return as-is rather than rejecting it
        return ucwords(strtolower(trim($input)));
    }
}
