<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TravelokaApiService
{
    protected $baseUrl = 'https://www.traveloka.com/api/v1';
    
    public function searchHotels(string $location, string $checkIn, string $checkOut, int $guests = 1)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/hotel/search', [
                'location' => $location,
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
                'guests' => $guests,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::warning('Traveloka API request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Traveloka API error', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    public function getHotelDetails(string $hotelId)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/hotel/detail', [
                'hotelId' => $hotelId,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Traveloka hotel detail error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
