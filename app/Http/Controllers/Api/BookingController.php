<?php

namespace App\Http\Controllers\Api;

use App\Models\Hotel;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $booking = Booking::where('user_id', Auth::id())->with('hotel')->get();
        return $this->apiResponse();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'hotel_id'=>'required|exists:hotels,id',
            'check_in'=>'required|date|after_or_equal:today',
            'check_out'=>'required|date|after:check_in',
            'guests'=>'required|integer|min:1',
            'special_requests'=>'nullable|string',
        ]);

        if($validator->fails())
        {
        return $this->apiResponse(null, false, $validator->errors(), 400);
        }
        $hotel = Hotel::findOrFail($request->hotel_id);
        $days = (strtotime($request->check_out) - strtotime($request->check_in)) / (60*60*24);
        $totalPrice = $hotel->price_per_night * $days;
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'hotel_id' => $request->hotel_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests'=> $request->guests,
            'total_price' => $totalPrice,
            'special_requests' => $request->special_requests, 
        ]);
        return Response()->json($booking,200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::where('user_id',Auth::id())->findOrFail($id);
        if ($booking->status !== 'pending')
        {
            return Response()->json(['message' => 'cannot cancel a booking that is not pending'],400);
        }

        $booking->update(['status'=>'cancelled']);
        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
