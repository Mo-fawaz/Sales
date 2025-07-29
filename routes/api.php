<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FlightBookingController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\FlightPassengerController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\TouristPlaceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\StripeController;
use App\Models\FlightBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/users', [UserController::class, 'store']);
Route::get('/flights', [FlightController::class, 'index']);
Route::post('/flights', [FlightController::class, 'store']);
Route::post('/bookings', [FlightBookingController::class, 'store']);
Route::post('/flights/search-week', [FlightController::class, 'searchFlightsWithinWeek']);
Route::get('/accommodations/search', [AccommodationController::class, 'searchAccommodations']);
Route::post('/accommodations/book', [AccommodationController::class, 'bookAccommodation']);
Route::get('/accommodations', [AccommodationController::class, 'index']);
Route::get('/vehicles/search', [VehicleController::class, 'search']);


// with API :
Route::get('/flights/search', [\App\Http\Controllers\Api\FlightController::class, 'search']);
Route::post('/flights/search-week', [FlightController::class, 'searchFlightsWithinWeek']);
Route::post('/book', [FlightPassengerController::class, 'storePassengers']);
Route::post('/temp-booking', [FlightBookingController::class, 'store']);
//
Route::post('bookings/{id}/confirm', [FlightBookingController::class, 'confirm']);
Route::post('bookings/{id}/cancel', [FlightBookingController::class, 'cancel']);




// 🏨 Hotels Routes
Route::prefix('hotels')->controller(HotelController::class)->group(function () {
    Route::get('/', 'index');                   // GET     /api/hotels
    Route::post('/add', 'store');              // POST    /api/hotels/add
    Route::post('/{id}/update', 'update');     // POST    /api/hotels/{id}/update
    Route::delete('/{id}/delete', 'destroy');     // Delete    /api/hotels/{id}/delete

    Route::get('/{id}', 'show');               // GET     /api/hotels/{id}
});

// 🍽️ Restaurants Routes
Route::prefix('restaurants')->controller(RestaurantController::class)->group(function () {
    Route::get('/', 'index');                       // GET     /api/restaurants
    Route::post('/add', 'store');                  // POST    /api/restaurants/add
    Route::post('/{id}/update', 'update');         // POST    /api/restaurants/{id}/update
    Route::get('/{id}', 'show');                   // GET     /api/restaurants/{id}
    Route::delete('/{id}/delete', 'destroy');     // Delete    /api/restaurants/{id}/delete

});

// 🏞️ Tourist Places Routes
Route::prefix('tourist-places')->controller(TouristPlaceController::class)->group(function () {
    Route::get('/', 'index');                             // GET     /api/tourist-places
    Route::post('/add', 'store');                         // POST    /api/tourist-places/add
    Route::post('/{id}/update', 'update');                // POST    /api/tourist-places/{id}/update
    Route::get('/{id}', 'show');                          // GET     /api/tourist-places/{id}
    Route::delete('/{id}/delete', 'destroy');             // DELETE  /api/tourist-places/{id}/delete
});


// ❤️ Favorites Routes
Route::prefix('favorites')->controller(FavoriteController::class)->group(function () {
    Route::get('/', 'index');                    // GET     /api/favorites         => Get all favorites (optionally filtered by type)
    Route::post('/toggle', 'toggle');            // POST    /api/favorites/toggle  => Add/remove a favorite
});
// Route::prefix('favorites')->middleware('auth:sanctum')->controller(FavoriteController::class)->group(function () {
//     Route::get('/', 'index');
//     Route::post('/toggle', 'toggle');
// });


Route::get('stripe', [StripeController::class, 'index']);
Route::post('stripe/create-charge', [StripeController::class, 'createCharge'])->name('stripe.create-charge');
