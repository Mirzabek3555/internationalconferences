<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'certificate_number',
        'pdf_path',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    /**
     * Sertifikat tegishli bo'lgan maqola
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Yangi sertifikat raqamini generatsiya qilish
     */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $lastCertificate = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCertificate) {
            $lastNumber = (int) substr($lastCertificate->certificate_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "CERT-{$year}-" . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
