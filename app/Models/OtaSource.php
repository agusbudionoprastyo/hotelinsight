<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OtaSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'website_url',
        'is_active',
    ];

    protected $casts = [
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
}
