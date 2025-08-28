<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'star_rating',
        'latitude',
        'longitude',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'star_rating' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function hotelPrices(): HasMany
    {
        return $this->hasMany(HotelPrice::class);
    }

    public function hotelReviews(): HasMany
    {
        return $this->hasMany(HotelReview::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->hotelReviews()->avg('rating') ?? 0;
    }

    public function getLatestPricesAttribute()
    {
        return $this->hotelPrices()
            ->with('otaSource')
            ->where('is_available', true)
            ->orderBy('last_updated', 'desc')
            ->get()
            ->groupBy('ota_source_id');
    }
}
