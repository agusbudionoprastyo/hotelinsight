<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OtaSource;

class OtaSourceSeeder extends Seeder
{
    public function run(): void
    {
        $otaSources = [
            [
                'name' => 'Booking.com',
                'slug' => 'booking-com',
                'website_url' => 'https://www.booking.com',
                'is_active' => true,
            ],
            [
                'name' => 'Agoda',
                'slug' => 'agoda',
                'website_url' => 'https://www.agoda.com',
                'is_active' => true,
            ],
            [
                'name' => 'Expedia',
                'slug' => 'expedia',
                'website_url' => 'https://www.expedia.com',
                'is_active' => true,
            ],
            [
                'name' => 'Hotels.com',
                'slug' => 'hotels-com',
                'website_url' => 'https://www.hotels.com',
                'is_active' => true,
            ],
            [
                'name' => 'TripAdvisor',
                'slug' => 'tripadvisor',
                'website_url' => 'https://www.tripadvisor.com',
                'is_active' => true,
            ],
            [
                'name' => 'Airbnb',
                'slug' => 'airbnb',
                'website_url' => 'https://www.airbnb.com',
                'is_active' => true,
            ],
            [
                'name' => 'Traveloka',
                'slug' => 'traveloka',
                'website_url' => 'https://www.traveloka.com',
                'is_active' => true,
            ],
            [
                'name' => 'Tiket.com',
                'slug' => 'tiket-com',
                'website_url' => 'https://www.tiket.com',
                'is_active' => true,
            ],
            [
                'name' => 'Pegipegi',
                'slug' => 'pegipegi',
                'website_url' => 'https://www.pegipegi.com',
                'is_active' => true,
            ],
        ];

        foreach ($otaSources as $otaSource) {
            OtaSource::updateOrCreate(
                ['slug' => $otaSource['slug']],
                $otaSource
            );
        }
    }
}
