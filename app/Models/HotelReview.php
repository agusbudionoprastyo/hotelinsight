<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'ota_source_id',
        'reviewer_name',
        'rating',
        'review_text',
        'review_date',
        'review_url',
        'is_verified',
    ];

    protected $casts = [
        'rating' => 'integer',
        'review_date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function otaSource(): BelongsTo
    {
        return $this->belongsTo(OtaSource::class);
    }
}
