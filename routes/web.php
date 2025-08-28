<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::get('/', [HotelController::class, 'index'])->name('home');

Route::resource('hotels', HotelController::class)->only(['index', 'show', 'create', 'store']);
