<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'ota_source_id',
        'price',
        'currency',
        'check_in_date',
        'check_out_date',
        'room_type',
        'booking_url',
        'is_available',
        'last_updated',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'is_available' => 'boolean',
        'last_updated' => 'datetime',
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
