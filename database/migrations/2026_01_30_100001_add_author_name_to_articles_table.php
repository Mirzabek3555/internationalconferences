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
            // Mualliflar ismi - qo'lda kiritiladi
            $table->string('author_name')->nullable()->after('author_id');
            $table->string('author_affiliation')->nullable()->after('author_name'); // Muallif tashkiloti
            $table->string('content_path')->nullable()->after('pdf_path'); // Maqola matni uchun
            $table->string('formatted_pdf_path')->nullable()->after('content_path'); // Formatlangan PDF
        });

        // author_id ni ixtiyoriy qilish
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_affiliation', 'content_path', 'formatted_pdf_path']);
        });
    }
};
