<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AmadeusService
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.amadeus.client_id');
        $this->clientSecret = config('services.amadeus.client_secret');
        $env = config('services.amadeus.env', 'test');
        $this->baseUrl = $env === 'production' ? 'https://api.amadeus.com' : 'https://test.api.amadeus.com';
    }

    public function getAccessToken(): ?string
    {
        return Cache::remember('amadeus_access_token', 1700, function () {
            $resp = Http::asForm()->post($this->baseUrl . '/v1/security/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);
            if ($resp->successful()) {
                $data = $resp->json();
                return $data['access_token'] ?? null;
            }
            return null;
        });
    }

    public function getHotelOffersByGeo(float $lat, float $lng, ?string $currency = 'IDR'): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        $resp = Http::withToken($token)->get($this->baseUrl . '/v3/shopping/hotel-offers', [
            'latitude' => $lat,
            'longitude' => $lng,
            'radius' => 5,
            'radiusUnit' => 'KM',
            'currency' => $currency,
            'bestRateOnly' => true,
        ]);
        if (!$resp->successful()) return [];
        $data = $resp->json();
        return $data['data'] ?? [];
    }
}
