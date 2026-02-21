<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryCoverImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapping = [
            'UZB' => 'images/countries/uzbekistan.png',
            'RUS' => 'images/countries/russia.png',
            'KAZ' => 'images/countries/kazakhstan.png',
            'TUR' => 'images/countries/turkey.png',
            'CHN' => 'images/countries/china.png',
            'KOR' => 'images/countries/south_korea.png',
            'JPN' => 'images/countries/japan.png',
            'DEU' => 'images/countries/germany.png',
            'GBR' => 'images/countries/united_kingdom.png',
            'USA' => 'images/countries/usa.png',
            'FRA' => 'images/countries/france.png',
            'ITA' => 'images/countries/italy.png',
            'ESP' => 'images/countries/spain.png',
            'IND' => 'images/countries/india.png',
            'BRA' => 'images/countries/brazil.png',
            'TRR' => 'images/countries/turkmenistan.png',
        ];

        foreach ($mapping as $code => $image) {
            Country::where('code', $code)->update(['cover_image' => $image]);
        }

        $this->command->info('Country cover images updated successfully!');
    }
}
