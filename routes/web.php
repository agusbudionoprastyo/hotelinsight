<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::resource('hotels', HotelController::class);
Route::post('/hotels/{hotel}/amadeus-prices', [HotelController::class, 'fetchAmadeusPrices'])->name('hotels.fetchAmadeus');

// Google Places API Routes
Route::get('/api/hotels/search', [HotelController::class, 'searchFromApi'])->name('api.hotels.search');
Route::get('/api/hotels/{placeId}/details', [HotelController::class, 'getApiDetails'])->name('api.hotels.details');
Route::post('/api/hotels/import', [HotelController::class, 'importFromGooglePlaces'])->name('api.hotels.import');
Route::post('/api/hotels/ensure/{placeId}', [HotelController::class, 'ensureHotelByPlaceId'])->name('api.hotels.ensure');

// Search hotel by place_id
Route::get('/hotels/search', [HotelController::class, 'searchByPlaceId'])->name('hotels.search');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

Route::post('/hotels/{hotel}/fetch-ota-data', [HotelController::class, 'fetchOtaData'])->name('hotels.fetch-ota-data');
Route::get('/hotels/{hotel}/prices', [HotelController::class, 'getHotelPrices'])->name('hotels.prices');
Route::get('/hotels/{hotel}/reviews', [HotelController::class, 'getHotelReviews'])->name('hotels.reviews');
Route::post('/hotels/search', [HotelController::class, 'searchHotels'])->name('hotels.search');
