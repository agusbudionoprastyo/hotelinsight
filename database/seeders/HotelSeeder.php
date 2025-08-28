<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\HotelPrice;
use App\Models\HotelReview;
use App\Models\OtaSource;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Grand Hotel Jakarta',
                'description' => 'Hotel bintang 5 dengan fasilitas mewah di pusat Jakarta',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'phone' => '+62-21-1234-5678',
                'email' => 'info@grandhoteljakarta.com',
                'website' => 'https://grandhoteljakarta.com',
                'star_rating' => 5,
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            ],
            [
                'name' => 'Bali Paradise Resort',
                'description' => 'Resort eksklusif dengan pemandangan pantai di Bali',
                'address' => 'Jl. Pantai Kuta No. 45',
                'city' => 'Bali',
                'country' => 'Indonesia',
                'phone' => '+62-361-9876-5432',
                'email' => 'reservations@baliparadise.com',
                'website' => 'https://baliparadise.com',
                'star_rating' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
            ],
            [
                'name' => 'Surabaya Business Hotel',
                'description' => 'Hotel bisnis modern di jantung Surabaya',
                'address' => 'Jl. Tunjungan No. 67',
                'city' => 'Surabaya',
                'country' => 'Indonesia',
                'phone' => '+62-31-4567-8901',
                'email' => 'booking@surabayabusiness.com',
                'website' => 'https://surabayabusiness.com',
                'star_rating' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
            ],
        ];

        foreach ($hotels as $hotelData) {
            $hotel = Hotel::create($hotelData);
            
            $this->createSamplePrices($hotel);
            $this->createSampleReviews($hotel);
        }
    }

    private function createSamplePrices($hotel)
    {
        $otaSources = OtaSource::all();
        $checkInDate = now()->addDays(7);
        $checkOutDate = now()->addDays(10);
        
        foreach ($otaSources->take(4) as $otaSource) {
            $basePrice = rand(800000, 2500000);
            
            HotelPrice::create([
                'hotel_id' => $hotel->id,
                'ota_source_id' => $otaSource->id,
                'price' => $basePrice,
                'currency' => 'IDR',
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'room_type' => 'Deluxe Room',
                'booking_url' => $otaSource->website_url,
                'is_available' => true,
                'last_updated' => now(),
            ]);
        }
    }

    private function createSampleReviews($hotel)
    {
        $reviews = [
            [
                'reviewer_name' => 'Ahmad Rahman',
                'rating' => 5,
                'review_text' => 'Hotel yang sangat nyaman dan pelayanan excellent!',
                'review_date' => now()->subDays(5),
            ],
            [
                'reviewer_name' => 'Sarah Johnson',
                'rating' => 4,
                'review_text' => 'Fasilitas bagus, lokasi strategis, recommended!',
                'review_date' => now()->subDays(10),
            ],
            [
                'reviewer_name' => 'Budi Santoso',
                'rating' => 5,
                'review_text' => 'Staff ramah, kamar bersih, harga worth it.',
                'review_date' => now()->subDays(15),
            ],
        ];

        foreach ($reviews as $reviewData) {
            $reviewData['hotel_id'] = $hotel->id;
            $reviewData['ota_source_id'] = OtaSource::inRandomOrder()->first()->id;
            
            HotelReview::create($reviewData);
        }
    }
}
