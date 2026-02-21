<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin foydalanuvchi yaratish
        User::create([
            'name' => 'Admin',
            'email' => 'admin@artiqle.uz',
            'password' => 'password',
            'role' => 'admin',
        ]);



        // Davlatlar yaratish
        $countries = [
            ['name' => 'O\'zbekiston', 'name_en' => 'Uzbekistan', 'code' => 'UZB', 'cover_image' => 'images/countries/uzbekistan.png'],
            ['name' => 'Rossiya', 'name_en' => 'Russia', 'code' => 'RUS', 'cover_image' => 'images/countries/russia.png'],
            ['name' => 'Qozog\'iston', 'name_en' => 'Kazakhstan', 'code' => 'KAZ', 'cover_image' => 'images/countries/kazakhstan.png'],
            ['name' => 'Turkiya', 'name_en' => 'Turkey', 'code' => 'TUR', 'cover_image' => 'images/countries/turkey.png'],
            ['name' => 'Xitoy', 'name_en' => 'China', 'code' => 'CHN', 'cover_image' => 'images/countries/china.png'],
            ['name' => 'Koreya', 'name_en' => 'South Korea', 'code' => 'KOR', 'cover_image' => 'images/countries/south_korea.png'],
            ['name' => 'Yaponiya', 'name_en' => 'Japan', 'code' => 'JPN', 'cover_image' => 'images/countries/japan.png'],
            ['name' => 'Germaniya', 'name_en' => 'Germany', 'code' => 'DEU', 'cover_image' => 'images/countries/germany.png'],
            ['name' => 'Angliya', 'name_en' => 'United Kingdom', 'code' => 'GBR', 'cover_image' => 'images/countries/united_kingdom.png'],
            ['name' => 'AQSh', 'name_en' => 'United States', 'code' => 'USA', 'cover_image' => 'images/countries/usa.png'],
            ['name' => 'Fransiya', 'name_en' => 'France', 'code' => 'FRA', 'cover_image' => 'images/countries/france.png'],
            ['name' => 'Italiya', 'name_en' => 'Italy', 'code' => 'ITA', 'cover_image' => 'images/countries/italy.png'],
            ['name' => 'Ispaniya', 'name_en' => 'Spain', 'code' => 'ESP', 'cover_image' => 'images/countries/spain.png'],
            ['name' => 'Hindiston', 'name_en' => 'India', 'code' => 'IND', 'cover_image' => 'images/countries/india.png'],
            ['name' => 'Braziliya', 'name_en' => 'Brazil', 'code' => 'BRA', 'cover_image' => 'images/countries/brazil.png'],
            ['name' => 'Kanada', 'name_en' => 'Canada', 'code' => 'CAN', 'cover_image' => 'images/countries/canada.png'],
            ['name' => 'Polsha', 'name_en' => 'Poland', 'code' => 'POL', 'cover_image' => 'images/countries/poland.png'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']], // Search by code
                array_merge($country, ['is_active' => true]) // Update/Create with these values
            );
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin login: admin@artiqle.uz / password');
    }
}
