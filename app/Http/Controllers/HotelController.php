<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\OtaSource;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with(['hotelReviews', 'hotelPrices.otaSource'])
            ->where('is_active', true)
            ->get()
            ->map(function ($hotel) {
                $hotel->average_rating = $hotel->average_rating;
                $hotel->latest_prices = $hotel->latest_prices;
                return $hotel;
            });

        return view('hotels.index', compact('hotels'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['hotelReviews.otaSource', 'hotelPrices.otaSource']);
        
        $hotel->average_rating = $hotel->average_rating;
        $hotel->latest_prices = $hotel->latest_prices;
        
        $reviews = $hotel->hotelReviews()
            ->with('otaSource')
            ->orderBy('review_date', 'desc')
            ->paginate(10);

        return view('hotels.show', compact('hotel', 'reviews'));
    }

    public function create()
    {
        $otaSources = OtaSource::where('is_active', true)->get();
        return view('hotels.create', compact('otaSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'image_url' => 'nullable|url|max:255',
        ]);

        $hotel = Hotel::create($validated);

        return redirect()->route('hotels.show', $hotel)
            ->with('success', 'Hotel berhasil ditambahkan!');
    }
}
