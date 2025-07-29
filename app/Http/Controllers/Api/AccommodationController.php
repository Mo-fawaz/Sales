<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\AccommodationBooking;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodation  = Accommodation::all();
        return response()->json([
            'success' => true,
            'accommodations' => $accommodation
        ]);
    }


    public function searchAccommodations(Request $request)
    {
        $query = Accommodation::query();

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price_per_night', [$request->min_price, $request->max_price]);
        }

        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        return response()->json([
            'success' => true,
            'accommodations' => $query->get()
        ]);
    }

    public function bookAccommodation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'accommodation_id' => 'required|exists:accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
        ]);

        $accommodation = Accommodation::find($request->accommodation_id);

        $nights = (new \Carbon\Carbon($request->check_in_date))->diffInDays(new \Carbon\Carbon($request->check_out_date));
        $totalPrice = $nights * $accommodation->price_per_night;

        $booking = AccommodationBooking::create([
            'user_id' => $request->user_id,
            'accommodation_id' => $request->accommodation_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'number_of_guests' => $request->number_of_guests,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حجز السكن بنجاح',
            'booking' => $booking
        ]);
    }
}
