<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // O'zbek tilida nomi
            $table->string('name_en');        // Ingliz tilida nomi
            $table->string('code', 3);        // Davlat kodi (UZB, USA, etc.)
            $table->string('flag_url')->nullable(); // Bayroq rasmi
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
