<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Services\HotelDataAggregatorService;
use App\Services\SerpApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HotelController extends Controller
{
    protected $hotelDataAggregator;
    protected $serpApiService;
    
    public function __construct(HotelDataAggregatorService $hotelDataAggregator, SerpApiService $serpApiService)
    {
        $this->hotelDataAggregator = $hotelDataAggregator;
        $this->serpApiService = $serpApiService;
    }
    
    public function index(): View
    {
        $hotels = Hotel::with(['latestPrices', 'reviews'])->latest()->paginate(10);
        return view('hotels.index', compact('hotels'));
    }
    
    public function show(Hotel $hotel): View
    {
        $hotel->load(['prices.otaSource', 'reviews.otaSource']);
        $aggregatedData = $this->hotelDataAggregator->getAggregatedHotelData($hotel);
        
        return view('hotels.show', compact('hotel', 'aggregatedData'));
    }
    
    public function create(): View
    {
        return view('hotels.create');
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $hotel = Hotel::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Hotel created successfully',
            'hotel' => $hotel
        ]);
    }
    
    public function fetchOtaData(Request $request, Hotel $hotel): JsonResponse
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);
        
        try {
            $aggregatedData = $this->hotelDataAggregator->aggregateHotelData(
                $hotel,
                $validated['check_in'],
                $validated['check_out']
            );
            
            return response()->json([
                'success' => true,
                'message' => 'OTA data fetched and aggregated successfully',
                'data' => $aggregatedData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch OTA data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function searchHotels(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:3',
            'location' => 'nullable|string',
        ]);
        
        try {
            $query = $validated['query'];
            $location = $validated['location'] ?? 'Indonesia';
            
            $searchQuery = "{$query} hotel {$location}";
            
            $response = $this->serpApiService->searchHotelPrices(
                $query,
                $location,
                now()->addDays(1)->format('Y-m-d'),
                now()->addDays(2)->format('Y-m-d')
            );
            
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getHotelPrices(Hotel $hotel, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);
        
        try {
            $prices = $hotel->prices()
                ->with('otaSource')
                ->where('check_in_date', $validated['check_in'])
                ->where('check_out_date', $validated['check_out'])
                ->get()
                ->groupBy('ota_source_id');
                
            $otaSources = \App\Models\OtaSource::where('is_active', true)->get();
            $priceData = [];
            
            foreach ($otaSources as $otaSource) {
                $otaPrices = $prices->get($otaSource->id, collect());
                $priceData[$otaSource->slug] = [
                    'name' => $otaSource->name,
                    'website_url' => $otaSource->website_url,
                    'prices' => $otaPrices->map(function ($price) {
                        return [
                            'price' => $price->price,
                            'currency' => $price->currency,
                            'room_type' => $price->room_type,
                            'booking_url' => $price->booking_url,
                            'last_updated' => $price->last_updated,
                        ];
                    }),
                    'lowest_price' => $otaPrices->min('price'),
                    'highest_price' => $otaPrices->max('price'),
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $priceData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get hotel prices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getHotelReviews(Hotel $hotel): JsonResponse
    {
        try {
            $reviews = $hotel->reviews()
                ->with('otaSource')
                ->latest()
                ->get()
                ->groupBy('ota_source_id');
                
            $otaSources = \App\Models\OtaSource::where('is_active', true)->get();
            $reviewData = [];
            
            foreach ($otaSources as $otaSource) {
                $otaReviews = $reviews->get($otaSource->id, collect());
                $reviewData[$otaSource->slug] = [
                    'name' => $otaSource->name,
                    'website_url' => $otaSource->website_url,
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
            
            return response()->json([
                'success' => true,
                'data' => $reviewData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get hotel reviews: ' . $e->getMessage()
            ], 500);
        }
    }
}
