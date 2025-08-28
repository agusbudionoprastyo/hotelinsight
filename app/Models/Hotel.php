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
        'location',
        'description',
        'rating',
        'place_id'
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(HotelPrice::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(HotelReview::class);
    }

    public function latestPrices()
    {
        return $this->hasMany(HotelPrice::class)->latest();
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getLatestPricesAttribute()
    {
        return $this->prices()->with('otaSource')->latest()->take(5)->get();
    }
}
