<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\HotelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('hotels', HotelController::class);
Route::resources('bookings' ,BookingController::class);
Route::post('/check-availability',[BookingController::class,'checkAvailability'])->name('check.availbility');
