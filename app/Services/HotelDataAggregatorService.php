<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelPrice;
use App\Models\HotelReview;
use App\Models\OtaSource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HotelDataAggregatorService
{
    protected $serpApiService;
    
    public function __construct(SerpApiService $serpApiService)
    {
        $this->serpApiService = $serpApiService;
    }
    
    public function aggregateHotelData(Hotel $hotel, string $checkIn, string $checkOut): array
    {
        try {
            DB::beginTransaction();
            
            $otaData = $this->serpApiService->searchHotelPrices(
                $hotel->name,
                $hotel->location,
                $checkIn,
                $checkOut
            );
            
            $reviewsData = $this->serpApiService->searchHotelReviews(
                $hotel->name,
                $hotel->location
            );
            
            $aggregatedData = [
                'prices' => [],
                'reviews' => [],
                'overall_rating' => 0,
                'total_otas' => count($otaData)
            ];
            
            foreach ($otaData as $otaName => $data) {
                $otaSource = $this->getOrCreateOtaSource($otaName);
                
                if ($otaSource) {
                    $this->saveHotelPrices($hotel, $otaSource, $data, $checkIn, $checkOut);
                    $this->saveHotelReviews($hotel, $otaSource, $data);
                    
                    $aggregatedData['prices'] = array_merge($aggregatedData['prices'], $data['prices'] ?? []);
                    
                    if ($data['rating']) {
                        $aggregatedData['overall_rating'] += $data['rating'];
                    }
                }
            }
            
            if ($reviewsData) {
                $this->saveGeneralReviews($hotel, $reviewsData);
                $aggregatedData['reviews'] = $reviewsData;
            }
            
            if ($aggregatedData['total_otas'] > 0) {
                $aggregatedData['overall_rating'] = round($aggregatedData['overall_rating'] / $aggregatedData['total_otas'], 1);
            }
            
            $this->updateHotelRating($hotel, $aggregatedData['overall_rating']);
            
            DB::commit();
            
            Log::info('Hotel data aggregated successfully', [
                'hotel_id' => $hotel->id,
                'hotel_name' => $hotel->name,
                'total_prices' => count($aggregatedData['prices']),
                'total_reviews' => count($aggregatedData['reviews'])
            ]);
            
            return $aggregatedData;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to aggregate hotel data', [
                'hotel_id' => $hotel->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    protected function getOrCreateOtaSource(string $otaName): ?OtaSource
    {
        $slug = str_replace(['.', ' '], ['-', '-'], strtolower($otaName));
        
        return OtaSource::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => ucfirst($otaName),
                'website_url' => "https://www.{$otaName}",
                'is_active' => true
            ]
        );
    }
    
    protected function saveHotelPrices(Hotel $hotel, OtaSource $otaSource, array $data, string $checkIn, string $checkOut): void
    {
        if (empty($data['prices'])) {
            return;
        }
        
        foreach ($data['prices'] as $priceData) {
            HotelPrice::updateOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'ota_source_id' => $otaSource->id,
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkOut,
                ],
                [
                    'price' => $priceData['price'],
                    'currency' => $priceData['currency'] ?? 'IDR',
                    'room_type' => 'Standard',
                    'booking_url' => $priceData['url'] ?? null,
                    'is_available' => true,
                    'last_updated' => now(),
                ]
            );
        }
    }
    
    protected function saveHotelReviews(Hotel $hotel, OtaSource $otaSource, array $data): void
    {
        if (empty($data['reviews'])) {
            return;
        }
        
        foreach ($data['reviews'] as $reviewData) {
            HotelReview::updateOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'ota_source_id' => $otaSource->id,
                    'reviewer_name' => 'OTA User',
                ],
                [
                    'rating' => $reviewData['rating'],
                    'review_text' => $reviewData['snippet'] ?? 'Review from ' . $otaSource->name,
                    'review_date' => now(),
                    'review_url' => $reviewData['url'] ?? null,
                    'is_verified' => false,
                ]
            );
        }
    }
    
    protected function saveGeneralReviews(Hotel $hotel, array $reviewsData): void
    {
        foreach ($reviewsData as $reviewData) {
            HotelReview::create([
                'hotel_id' => $hotel->id,
                'ota_source_id' => null,
                'reviewer_name' => 'General User',
                'rating' => $reviewData['rating'],
                'review_text' => $reviewData['snippet'],
                'review_date' => now(),
                'review_url' => $reviewData['url'],
                'is_verified' => false,
            ]);
        }
    }
    
    protected function updateHotelRating(Hotel $hotel, float $rating): void
    {
        if ($rating > 0) {
            $hotel->update(['rating' => $rating]);
        }
    }
    
    public function getAggregatedHotelData(Hotel $hotel): array
    {
        $prices = $hotel->prices()
            ->with('otaSource')
            ->latest()
            ->get()
            ->groupBy('ota_source_id');
            
        $reviews = $hotel->reviews()
            ->with('otaSource')
            ->latest()
            ->get()
            ->groupBy('ota_source_id');
            
        $otaSources = OtaSource::where('is_active', true)->get();
        
        $aggregatedData = [];
        
        foreach ($otaSources as $otaSource) {
            $otaPrices = $prices->get($otaSource->id, collect());
            $otaReviews = $reviews->get($otaSource->id, collect());
            
            $aggregatedData[$otaSource->slug] = [
                'name' => $otaSource->name,
                'website_url' => $otaSource->website_url,
                'prices' => $otaPrices->map(function ($price) {
                    return [
                        'price' => $price->price,
                        'currency' => $price->currency,
                        'room_type' => $price->room_type,
                        'check_in' => $price->check_in_date,
                        'check_out' => $price->check_out_date,
                        'booking_url' => $price->booking_url,
                        'last_updated' => $price->last_updated,
                    ];
                }),
                'reviews' => $otaReviews->map(function ($review) {
                    return [
                        'rating' => $review->rating,
                        'review_text' => $review->review_text,
                        'reviewer_name' => $review->reviewer_name,
                        'review_date' => $review->review_date,
                        'review_url' => $review->review_url,
                    ];
                }),
                'average_rating' => $otaReviews->avg('rating') ?? 0,
                'total_reviews' => $otaReviews->count(),
            ];
        }
        
        return $aggregatedData;
    }
}
