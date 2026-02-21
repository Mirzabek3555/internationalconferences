<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'conference_date',
        'month_year',
        'status',
        'collection_pdf_path',
        'pdf_collection_path',
        'is_completed',
    ];

    protected $casts = [
        'conference_date' => 'date',
        'is_completed' => 'boolean',
    ];

    /**
     * Konferensiya tegishli bo'lgan davlat
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Konferensiyaga tegishli maqolalar
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class)->orderBy('order_number');
    }

    /**
     * Formatlangan sana
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->conference_date->format('d.m.Y');
    }

    /**
     * Nashr etilgan maqolalar soni
     */
    public function getPublishedArticlesCountAttribute(): int
    {
        return $this->articles()->where('status', 'published')->count();
    }

    /**
     * Faqat faol konferensiyalar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Faqat yakunlangan konferensiyalar
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
