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
        Schema::table('articles', function (Blueprint $table) {
            // Kalit so'zlar - akademik maqola uchun zarur
            if (!Schema::hasColumn('articles', 'keywords')) {
                $table->string('keywords', 500)->nullable()->after('abstract');
            }

            // Adabiyotlar ro'yxati
            if (!Schema::hasColumn('articles', 'references')) {
                $table->text('references')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['keywords', 'references']);
        });
    }
};
