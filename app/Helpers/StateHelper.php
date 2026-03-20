<?php

namespace App\Helpers;

class StateHelper
{
    /**
     * Returns states/provinces for a given country name.
     * Returns an empty array if no data is available (use free-text input instead).
     */
    public static function forCountry(string $country): array
    {
        return self::all()[$country] ?? [];
    }

    /**
     * Returns true if we have state data for the given country.
     */
    public static function hasStates(string $country): bool
    {
        return !empty(self::all()[$country]);
    }

    /**
     * Full list of states/provinces keyed by country name.
     */
    public static function all(): array
    {
        return [
            'United States' => [
                'Alabama','Alaska','Arizona','Arkansas','California','Colorado',
                'Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho',
                'Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana',
                'Maine','Maryland','Massachusetts','Michigan','Minnesota',
                'Mississippi','Missouri','Montana','Nebraska','Nevada',
                'New Hampshire','New Jersey','New Mexico','New York',
                'North Carolina','North Dakota','Ohio','Oklahoma','Oregon',
                'Pennsylvania','Rhode Island','South Carolina','South Dakota',
                'Tennessee','Texas','Utah','Vermont','Virginia','Washington',
                'West Virginia','Wisconsin','Wyoming',
            ],
            'Canada' => [
                'Alberta','British Columbia','Manitoba','New Brunswick',
                'Newfoundland and Labrador','Northwest Territories','Nova Scotia',
                'Nunavut','Ontario','Prince Edward Island','Quebec','Saskatchewan','Yukon',
            ],
            'Australia' => [
                'Australian Capital Territory','New South Wales','Northern Territory',
                'Queensland','South Australia','Tasmania','Victoria','Western Australia',
            ],
            'United Kingdom' => [
                'England','Northern Ireland','Scotland','Wales',
                'Greater London','South East England','South West England',
                'East of England','East Midlands','West Midlands',
                'Yorkshire and the Humber','North West England','North East England',
            ],
            'India' => [
                'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
                'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
                'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
                'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana',
                'Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli',
                'Daman and Diu','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep',
                'Puducherry',
            ],
            'Nigeria' => [
                'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue',
                'Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe',
                'Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos',
                'Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto',
                'Taraba','Yobe','Zamfara','FCT Abuja',
            ],
            'Ghana' => [
                'Ahafo','Ashanti','Bono','Bono East','Central','Eastern','Greater Accra',
                'North East','Northern','Oti','Savannah','Upper East','Upper West','Volta',
                'Western','Western North',
            ],
            'South Africa' => [
                'Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo',
                'Mpumalanga','North West','Northern Cape','Western Cape',
            ],
            'Kenya' => [
                'Baringo','Bomet','Bungoma','Busia','Elgeyo-Marakwet','Embu','Garissa',
                'Homa Bay','Isiolo','Kajiado','Kakamega','Kericho','Kiambu','Kilifi',
                'Kirinyaga','Kisii','Kisumu','Kitui','Kwale','Laikipia','Lamu','Machakos',
                'Makueni','Mandera','Marsabit','Meru','Migori','Mombasa','Muranga',
                'Nairobi','Nakuru','Nandi','Narok','Nyamira','Nyandarua','Nyeri',
                'Samburu','Siaya','Taita-Taveta','Tana River','Tharaka-Nithi','Trans-Nzoia',
                'Turkana','Uasin Gishu','Vihiga','Wajir','West Pokot',
            ],
            'Brazil' => [
                'Acre','Alagoas','Amapá','Amazonas','Bahia','Ceará','Distrito Federal',
                'Espírito Santo','Goiás','Maranhão','Mato Grosso','Mato Grosso do Sul',
                'Minas Gerais','Pará','Paraíba','Paraná','Pernambuco','Piauí',
                'Rio de Janeiro','Rio Grande do Norte','Rio Grande do Sul','Rondônia',
                'Roraima','Santa Catarina','São Paulo','Sergipe','Tocantins',
            ],
            'Mexico' => [
                'Aguascalientes','Baja California','Baja California Sur','Campeche',
                'Chiapas','Chihuahua','Ciudad de México','Coahuila','Colima','Durango',
                'Estado de México','Guanajuato','Guerrero','Hidalgo','Jalisco','Michoacán',
                'Morelos','Nayarit','Nuevo León','Oaxaca','Puebla','Querétaro',
                'Quintana Roo','San Luis Potosí','Sinaloa','Sonora','Tabasco','Tamaulipas',
                'Tlaxcala','Veracruz','Yucatán','Zacatecas',
            ],
            'Philippines' => [
                'Abra','Agusan del Norte','Agusan del Sur','Aklan','Albay','Antique',
                'Apayao','Aurora','Basilan','Bataan','Batanes','Batangas','Benguet',
                'Biliran','Bohol','Bukidnon','Bulacan','Cagayan','Camarines Norte',
                'Camarines Sur','Camiguin','Capiz','Catanduanes','Cavite','Cebu',
                'Cotabato','Davao de Oro','Davao del Norte','Davao del Sur',
                'Davao Occidental','Davao Oriental','Dinagat Islands','Eastern Samar',
                'Guimaras','Ifugao','Ilocos Norte','Ilocos Sur','Iloilo','Isabela',
                'Kalinga','La Union','Laguna','Lanao del Norte','Lanao del Sur','Leyte',
                'Maguindanao del Norte','Maguindanao del Sur','Marinduque','Masbate',
                'Metro Manila','Misamis Occidental','Misamis Oriental','Mountain Province',
                'Negros Occidental','Negros Oriental','Northern Samar','Nueva Ecija',
                'Nueva Vizcaya','Occidental Mindoro','Oriental Mindoro','Palawan',
                'Pampanga','Pangasinan','Quezon','Quirino','Rizal','Romblon','Samar',
                'Sarangani','Siquijor','Sorsogon','South Cotabato','Southern Leyte',
                'Sultan Kudarat','Sulu','Surigao del Norte','Surigao del Sur','Tarlac',
                'Tawi-Tawi','Zambales','Zamboanga del Norte','Zamboanga del Sur',
                'Zamboanga Sibugay',
            ],
            'Pakistan' => [
                'Azad Kashmir','Balochistan','Gilgit-Baltistan','Islamabad Capital Territory',
                'Khyber Pakhtunkhwa','Punjab','Sindh',
            ],
            'Bangladesh' => [
                'Barisal','Chittagong','Dhaka','Khulna','Mymensingh','Rajshahi','Rangpur','Sylhet',
            ],
            'Indonesia' => [
                'Aceh','Bali','Bangka Belitung','Banten','Bengkulu','Central Java',
                'Central Kalimantan','Central Sulawesi','East Java','East Kalimantan',
                'East Nusa Tenggara','Gorontalo','Jakarta','Jambi','Lampung','Maluku',
                'North Kalimantan','North Maluku','North Sulawesi','North Sumatra',
                'Papua','Riau','Riau Islands','South Kalimantan','South Sulawesi',
                'South Sumatra','Southeast Sulawesi','West Java','West Kalimantan',
                'West Nusa Tenggara','West Papua','West Sulawesi','West Sumatra',
                'Yogyakarta',
            ],
            'Germany' => [
                'Baden-Württemberg','Bavaria','Berlin','Brandenburg','Bremen',
                'Hamburg','Hesse','Lower Saxony','Mecklenburg-Vorpommern',
                'North Rhine-Westphalia','Rhineland-Palatinate','Saarland',
                'Saxony','Saxony-Anhalt','Schleswig-Holstein','Thuringia',
            ],
            'France' => [
                'Auvergne-Rhône-Alpes','Bourgogne-Franche-Comté','Bretagne','Centre-Val de Loire',
                'Corse','Grand Est','Guadeloupe','Guyane','Hauts-de-France',
                'Île-de-France','La Réunion','Martinique','Mayotte','Normandie',
                'Nouvelle-Aquitaine','Occitanie','Pays de la Loire',
                "Provence-Alpes-Côte d'Azur",
            ],
            'Italy' => [
                'Abruzzo','Aosta Valley','Basilicata','Calabria','Campania','Emilia-Romagna',
                'Friuli Venezia Giulia','Lazio','Liguria','Lombardy','Marche','Molise',
                'Piedmont','Puglia','Sardinia','Sicily','Trentino-South Tyrol','Tuscany',
                'Umbria','Veneto',
            ],
            'Spain' => [
                'Andalusia','Aragon','Asturias','Balearic Islands','Basque Country',
                'Canary Islands','Cantabria','Castilla-La Mancha','Castile and León',
                'Catalonia','Ceuta','Extremadura','Galicia','La Rioja','Madrid',
                'Melilla','Murcia','Navarre','Valencia',
            ],
            'Russia' => [
                'Altai Republic','Buryatia','Chechnya','Dagestan','Ingushetia',
                'Kabardino-Balkaria','Kalmykia','Karachay-Cherkessia','Karelia',
                'Khakassia','Komi','Mari El','Mordovia','North Ossetia','Sakha',
                'Tatarstan','Tuva','Udmurtia','Bashkortostan','Adygea','Moscow',
                'Saint Petersburg','Altai Krai','Krasnodar','Krasnoyarsk','Primorsky',
                'Stavropol','Khabarovsk',
            ],
            'China' => [
                'Anhui','Beijing','Chongqing','Fujian','Gansu','Guangdong','Guangxi',
                'Guizhou','Hainan','Hebei','Heilongjiang','Henan','Hong Kong','Hubei',
                'Hunan','Inner Mongolia','Jiangsu','Jiangxi','Jilin','Liaoning','Macau',
                'Ningxia','Qinghai','Shaanxi','Shandong','Shanghai','Shanxi','Sichuan',
                'Tianjin','Tibet','Xinjiang','Yunnan','Zhejiang',
            ],
            'Japan' => [
                'Aichi','Akita','Aomori','Chiba','Ehime','Fukui','Fukuoka','Fukushima',
                'Gifu','Gunma','Hiroshima','Hokkaido','Hyogo','Ibaraki','Ishikawa',
                'Iwate','Kagawa','Kagoshima','Kanagawa','Kochi','Kumamoto','Kyoto',
                'Mie','Miyagi','Miyazaki','Nagano','Nagasaki','Nara','Niigata','Oita',
                'Okayama','Okinawa','Osaka','Saga','Saitama','Shiga','Shimane',
                'Shizuoka','Tochigi','Tokushima','Tokyo','Tottori','Toyama','Wakayama',
                'Yamagata','Yamaguchi','Yamanashi',
            ],
            'South Korea' => [
                'Busan','Chungcheongbuk-do','Chungcheongnam-do','Daegu','Daejeon','Gangwon-do',
                'Gwangju','Gyeonggi-do','Gyeongsangbuk-do','Gyeongsangnam-do','Incheon',
                'Jeju','Jeollabuk-do','Jeollanam-do','Sejong','Seoul','Ulsan',
            ],
            'New Zealand' => [
                'Auckland','Bay of Plenty','Canterbury','Gisborne','Hawke\'s Bay',
                'Manawatū-Whanganui','Marlborough','Nelson','Northland','Otago',
                'Southland','Taranaki','Tasman','Waikato','Wellington','West Coast',
            ],
            'Zimbabwe' => [
                'Bulawayo','Harare','Manicaland','Mashonaland Central','Mashonaland East',
                'Mashonaland West','Masvingo','Matabeleland North','Matabeleland South','Midlands',
            ],
            'Zambia' => [
                'Central','Copperbelt','Eastern','Luapula','Lusaka','Muchinga',
                'North-Western','Northern','Southern','Western',
            ],
            'Ethiopia' => [
                'Addis Ababa','Afar','Amhara','Benishangul-Gumuz','Dire Dawa','Gambela',
                'Harari','Oromia','Sidama','Somali','South West Ethiopia Peoples','SNNPR','Tigray',
            ],
            'Tanzania' => [
                'Arusha','Dar es Salaam','Dodoma','Geita','Iringa','Kagera','Katavi',
                'Kigoma','Kilimanjaro','Lindi','Manyara','Mara','Mbeya','Morogoro',
                'Mtwara','Mwanza','Njombe','Pemba North','Pemba South','Pwani','Rukwa',
                'Ruvuma','Shinyanga','Simiyu','Singida','Songwe','Tabora','Tanga',
                'Zanzibar North','Zanzibar South and Central','Zanzibar West',
            ],
            'Uganda' => [
                'Central Region','Eastern Region','Northern Region','Western Region',
            ],
            'Argentina' => [
                'Buenos Aires','Buenos Aires Province','Catamarca','Chaco','Chubut',
                'Córdoba','Corrientes','Entre Ríos','Formosa','Jujuy','La Pampa','La Rioja',
                'Mendoza','Misiones','Neuquén','Río Negro','Salta','San Juan','San Luis',
                'Santa Cruz','Santa Fe','Santiago del Estero','Tierra del Fuego','Tucumán',
            ],
            'Colombia' => [
                'Amazonas','Antioquia','Arauca','Atlántico','Bolívar','Boyacá','Caldas',
                'Caquetá','Casanare','Cauca','Cesar','Chocó','Córdoba','Cundinamarca',
                'Guainía','Guaviare','Huila','La Guajira','Magdalena','Meta','Nariño',
                'Norte de Santander','Putumayo','Quindío','Risaralda','San Andrés y Providencia',
                'Santander','Sucre','Tolima','Valle del Cauca','Vaupés','Vichada',
            ],
        ];
    }
}
