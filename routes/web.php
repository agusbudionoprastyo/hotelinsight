<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('hotels', HotelController::class);

// Google Places API Routes
Route::get('/api/hotels/search', [HotelController::class, 'searchFromApi'])->name('api.hotels.search');
Route::get('/api/hotels/{placeId}/details', [HotelController::class, 'getApiDetails'])->name('api.hotels.details');
Route::post('/api/hotels/import', [HotelController::class, 'importFromGooglePlaces'])->name('api.hotels.import');
