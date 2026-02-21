<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kanada va Polsha davlatlarini qo'shish
        $countries = [
            [
                'name' => 'Kanada',
                'name_en' => 'Canada',
                'code' => 'CAN',
                'cover_image' => 'images/countries/canada.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Polsha',
                'name_en' => 'Poland',
                'code' => 'POL',
                'cover_image' => 'images/countries/poland.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($countries as $country) {
            // Agar mavjud bo'lmasa qo'shish
            $exists = DB::table('countries')->where('code', $country['code'])->exists();
            if (!$exists) {
                DB::table('countries')->insert($country);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('countries')->whereIn('code', ['CAN', 'POL'])->delete();
    }
};
