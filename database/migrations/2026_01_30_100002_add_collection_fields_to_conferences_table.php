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
        Schema::table('conferences', function (Blueprint $table) {
            $table->string('pdf_collection_path')->nullable()->after('status'); // Yakuniy to'plam PDF
            $table->boolean('is_completed')->default(false)->after('pdf_collection_path'); // Konferensiya yakunlanganmi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn(['pdf_collection_path', 'is_completed']);
        });
    }
};
