<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'conference_name',
        'conference_description',
        'code',
        'flag_url',
        'cover_image',
        'schedule_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Davlatga tegishli konferensiyalar
     */
    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    /**
     * Davlatga tegishli maqolalar (konferensiya orqali)
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Article::class, Conference::class);
    }

    /**
     * Faqat faol davlatlarni olish
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
