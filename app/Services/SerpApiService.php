<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SerpApiService
{
    protected $apiKey;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.serpapi.api_key');
        $this->baseUrl = config('services.serpapi.base_url');
    }
    
    public function searchHotelPrices(string $hotelName, string $location, string $checkIn, string $checkOut)
    {
        $cacheKey = "hotel_prices_{$hotelName}_{$location}_{$checkIn}_{$checkOut}";
        
        return Cache::remember($cacheKey, 3600, function () use ($hotelName, $location, $checkIn, $checkOut) {
            $otaData = [];
            
            $otas = [
                'booking.com' => 'booking.com',
                'agoda' => 'agoda.com',
                'expedia' => 'expedia.com',
                'traveloka' => 'traveloka.com',
                'tiket.com' => 'tiket.com'
            ];
            
            foreach ($otas as $otaName => $otaDomain) {
                $data = $this->searchOtaHotel($hotelName, $location, $otaDomain, $checkIn, $checkOut);
                if ($data) {
                    $otaData[$otaName] = $data;
                }
            }
            
            return $otaData;
        });
    }
    
    protected function searchOtaHotel(string $hotelName, string $location, string $otaDomain, string $checkIn, string $checkOut)
    {
        try {
            $query = "{$hotelName} {$location} hotel {$otaDomain} price {$checkIn} {$checkOut}";
            
            $response = Http::timeout(30)->get($this->baseUrl, [
                'q' => $query,
                'api_key' => $this->apiKey,
                'engine' => 'google',
                'num' => 10,
                'gl' => 'id',
                'hl' => 'en'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseOtaResults($data, $otaDomain);
            }
            
            Log::warning("SerpAPI request failed for {$otaDomain}", [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error("SerpAPI error for {$otaDomain}", ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    protected function parseOtaResults(array $data, string $otaDomain): array
    {
        $results = [
            'ota_name' => $otaDomain,
            'prices' => [],
            'reviews' => [],
            'rating' => null,
            'booking_url' => null
        ];
        
        if (isset($data['organic_results'])) {
            foreach ($data['organic_results'] as $result) {
                if (isset($result['title']) && isset($result['link'])) {
                    $price = $this->extractPrice($result['title'], $result['snippet'] ?? '');
                    $rating = $this->extractRating($result['title'], $result['snippet'] ?? '');
                    
                    if ($price) {
                        $results['prices'][] = [
                            'price' => $price,
                            'currency' => 'IDR',
                            'source' => $result['title'],
                            'url' => $result['link']
                        ];
                    }
                    
                    if ($rating && !$results['rating']) {
                        $results['rating'] = $rating;
                    }
                    
                    if (!$results['booking_url'] && str_contains($result['link'], $otaDomain)) {
                        $results['booking_url'] = $result['link'];
                    }
                }
            }
        }
        
        return $results;
    }
    
    protected function extractPrice(string $title, string $snippet): ?float
    {
        $text = $title . ' ' . $snippet;
        
        if (preg_match('/Rp\s*([\d,]+(?:\.\d{3})*(?:,\d{2})?)/', $text, $matches)) {
            $price = str_replace(['Rp', ',', '.'], '', $matches[1]);
            return (float) $price;
        }
        
        if (preg_match('/(\d+(?:\.\d{3})*(?:,\d{2})?)\s*IDR/', $text, $matches)) {
            $price = str_replace(['IDR', ',', '.'], '', $matches[1]);
            return (float) $price;
        }
        
        return null;
    }
    
    protected function extractRating(string $title, string $snippet): ?float
    {
        $text = $title . ' ' . $snippet;
        
        if (preg_match('/(\d+(?:\.\d)?)\s*\/\s*5/', $text, $matches)) {
            return (float) $matches[1];
        }
        
        if (preg_match('/(\d+(?:\.\d)?)\s*stars?/i', $text, $matches)) {
            return (float) $matches[1];
        }
        
        return null;
    }
    
    public function searchHotelReviews(string $hotelName, string $location)
    {
        $cacheKey = "hotel_reviews_{$hotelName}_{$location}";
        
        return Cache::remember($cacheKey, 7200, function () use ($hotelName, $location) {
            try {
                $query = "{$hotelName} {$location} hotel reviews ratings";
                
                $response = Http::timeout(30)->get($this->baseUrl, [
                    'q' => $query,
                    'api_key' => $this->apiKey,
                    'engine' => 'google',
                    'num' => 20,
                    'gl' => 'id',
                    'hl' => 'en'
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $this->parseReviewResults($data);
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('SerpAPI review search error', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }
    
    protected function parseReviewResults(array $data): array
    {
        $reviews = [];
        
        if (isset($data['organic_results'])) {
            foreach ($data['organic_results'] as $result) {
                if (isset($result['title']) && isset($result['snippet'])) {
                    $rating = $this->extractRating($result['title'], $result['snippet']);
                    
                    if ($rating) {
                        $reviews[] = [
                            'rating' => $rating,
                            'title' => $result['title'],
                            'snippet' => $result['snippet'],
                            'url' => $result['link'] ?? null
                        ];
                    }
                }
            }
        }
        
        return $reviews;
    }
}
