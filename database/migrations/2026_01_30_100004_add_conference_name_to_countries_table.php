<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Har bir davlat uchun bitta konferensiya nomi
            $table->string('conference_name')->nullable()->after('name_en');
            $table->text('conference_description')->nullable()->after('conference_name');
        });

        // Default nom qo'yish
        \DB::table('countries')->update([
            'conference_name' => 'Bu yerda konferensiya nomi yoziladi'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['conference_name', 'conference_description']);
        });
    }
};
