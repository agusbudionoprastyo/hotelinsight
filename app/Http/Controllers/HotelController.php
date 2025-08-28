<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\OtaSource;
use App\Models\HotelPrice;
use App\Models\HotelReview;
use App\Services\GooglePlacesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    protected $googlePlacesService;

    public function __construct(GooglePlacesService $googlePlacesService)
    {
        $this->googlePlacesService = $googlePlacesService;
    }

    public function index()
    {
        $hotels = Hotel::with(['latestPrices', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();

        return view('hotels.index', compact('hotels'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['prices.otaSource', 'reviews.otaSource']);
        
        $reviews = $hotel->reviews()->with('otaSource')->paginate(10);
        
        $googleDetails = null;
        if ($hotel->place_id) {
            try {
                $googleDetails = $this->googlePlacesService->getHotelDetails($hotel->place_id);
            } catch (\Exception $e) {
                $googleDetails = null;
            }
        }
        
        return view('hotels.show', compact('hotel', 'reviews', 'googleDetails'));
    }

    public function create()
    {
        $otaSources = OtaSource::all();
        return view('hotels.create', compact('otaSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $hotel = Hotel::create($validated);

        return redirect()->route('hotels.show', $hotel)
            ->with('success', 'Hotel created successfully!');
    }

    public function searchByPlaceId(Request $request)
    {
        $placeId = $request->get('place_id');
        
        $hotel = Hotel::where('place_id', $placeId)->first();
        
        return response()->json([
            'success' => true,
            'hotel' => $hotel
        ]);
    }

    public function searchFromApi(Request $request)
    {
        $city = $request->get('city', 'Jakarta');
        $radius = $request->get('radius', 5000);

        try {
            $hotels = $this->googlePlacesService->searchHotels($city, $radius);
            
            return response()->json([
                'success' => true,
                'data' => $hotels,
                'city' => $city,
                'count' => count($hotels)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching hotels: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getApiDetails(Request $request, $placeId)
    {
        try {
            $hotelDetails = $this->googlePlacesService->getHotelDetails($placeId);
            
            if ($hotelDetails) {
                return response()->json([
                    'success' => true,
                    'data' => $hotelDetails
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching hotel details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importFromGooglePlaces(Request $request)
    {
        $city = $request->get('city', 'Jakarta');
        $radius = $request->get('radius', 5000);

        try {
            $apiHotels = $this->googlePlacesService->searchHotels($city, $radius);
            $importedCount = 0;

            foreach ($apiHotels as $apiHotel) {
                $hotel = Hotel::updateOrCreate(
                    ['place_id' => $apiHotel['place_id']],
                    [
                        'name' => $apiHotel['name'],
                        'location' => $apiHotel['address'],
                        'rating' => $apiHotel['rating'],
                        'description' => 'Imported from Google Places API'
                    ]
                );

                if ($hotel->wasRecentlyCreated) {
                    $importedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} hotels from Google Places API",
                'total_found' => count($apiHotels),
                'imported' => $importedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing hotels: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ensureHotelByPlaceId(string $placeId)
    {
        try {
            $details = $this->googlePlacesService->getHotelDetails($placeId);
            if (!$details) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel details not found'
                ], 404);
            }

            $hotel = Hotel::updateOrCreate(
                ['place_id' => $placeId],
                [
                    'name' => $details['name'] ?? 'Unknown',
                    'location' => $details['address'] ?? '',
                    'rating' => $details['rating'] ?? 0,
                    'description' => 'Imported from Google Places API'
                ]
            );

            return response()->json([
                'success' => true,
                'hotel_id' => $hotel->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ensuring hotel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchAmadeusPrices(Hotel $hotel)
    {
        try {
            if (!$hotel->latitude || !$hotel->longitude) {
                if ($hotel->place_id) {
                    $details = $this->googlePlacesService->getHotelDetails($hotel->place_id);
                    if (!empty($details['location']['lat']) && !empty($details['location']['lng'])) {
                        $hotel->latitude = $details['location']['lat'];
                        $hotel->longitude = $details['location']['lng'];
                        $hotel->save();
                    }
                }
            }

            if (!$hotel->latitude || !$hotel->longitude) {
                return back()->with('error', 'Koordinat hotel tidak tersedia');
            }

            $amadeus = app(\App\Services\AmadeusService::class);
            $offers = $amadeus->getHotelOffersByGeo((float)$hotel->latitude, (float)$hotel->longitude, 'IDR');

            $imported = 0;
            foreach ($offers as $offer) {
                $merchant = $offer['hotel']['name'] ?? 'Amadeus';
                $ota = \App\Models\OtaSource::firstOrCreate([
                    'slug' => str( $merchant )->slug('_'),
                ], [
                    'name' => $merchant,
                ]);

                $price = $offer['offers'][0]['price']['total'] ?? null;
                $currency = $offer['offers'][0]['price']['currency'] ?? 'IDR';
                if ($price) {
                    \App\Models\HotelPrice::create([
                        'hotel_id' => $hotel->id,
                        'ota_source_id' => $ota->id,
                        'price' => $price,
                        'currency' => $currency,
                        'check_in_date' => now()->addDays(7),
                        'check_out_date' => now()->addDays(8),
                        'room_type' => $offer['offers'][0]['room']['typeEstimated']['category'] ?? null,
                        'booking_url' => $offer['offers'][0]['self'] ?? null,
                        'is_available' => true,
                        'last_updated' => now(),
                    ]);
                    $imported++;
                }
            }

            return back()->with('success', "Harga Amadeus tersimpan: $imported item");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengambil harga Amadeus: ' . $e->getMessage());
        }
    }
}
