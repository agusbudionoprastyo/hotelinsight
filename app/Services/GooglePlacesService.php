<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GooglePlacesService
{
    private $apiKey;
    private $baseUrl = 'https://maps.googleapis.com/maps/api/place';

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key');
    }

    public function searchHotels($city, $radius = 5000)
    {
        $cacheKey = "hotels_search_{$city}_{$radius}";
        
        return Cache::remember($cacheKey, 3600, function () use ($city, $radius) {
            $response = Http::get("{$this->baseUrl}/textsearch/json", [
                'query' => "hotels in {$city}",
                'type' => 'lodging',
                'radius' => $radius,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatHotels($data['results'] ?? []);
            }

            return [];
        });
    }

    public function getHotelDetails($placeId)
    {
        $cacheKey = "hotel_details_{$placeId}";
        
        return Cache::remember($cacheKey, 7200, function () use ($placeId) {
            $response = Http::get("{$this->baseUrl}/details/json", [
                'place_id' => $placeId,
                'fields' => 'name,formatted_address,rating,user_ratings_total,photos,reviews,formatted_phone_number,website,opening_hours,price_level',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatHotelDetails($data['result'] ?? []);
            }

            return null;
        });
    }

    public function getHotelPhotos($photoReference, $maxWidth = 400)
    {
        return "{$this->baseUrl}/photo?maxwidth={$maxWidth}&photo_reference={$photoReference}&key={$this->apiKey}";
    }

    private function formatHotels($hotels)
    {
        return collect($hotels)->map(function ($hotel) {
            return [
                'place_id' => $hotel['place_id'],
                'name' => $hotel['name'],
                'address' => $hotel['formatted_address'],
                'rating' => $hotel['rating'] ?? 0,
                'user_ratings_total' => $hotel['user_ratings_total'] ?? 0,
                'price_level' => $hotel['price_level'] ?? null,
                'photos' => $hotel['photos'] ?? [],
                'location' => [
                    'lat' => $hotel['geometry']['location']['lat'],
                    'lng' => $hotel['geometry']['location']['lng']
                ]
            ];
        })->toArray();
    }

    private function formatHotelDetails($hotel)
    {
        return [
            'place_id' => $hotel['place_id'] ?? null,
            'name' => $hotel['name'] ?? '',
            'address' => $hotel['formatted_address'] ?? '',
            'rating' => $hotel['rating'] ?? 0,
            'user_ratings_total' => $hotel['user_ratings_total'] ?? 0,
            'price_level' => $hotel['price_level'] ?? null,
            'phone' => $hotel['formatted_phone_number'] ?? '',
            'website' => $hotel['website'] ?? '',
            'opening_hours' => $hotel['opening_hours'] ?? null,
            'photos' => $hotel['photos'] ?? [],
            'reviews' => $hotel['reviews'] ?? []
        ];
    }
}
