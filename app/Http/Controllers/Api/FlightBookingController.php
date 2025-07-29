<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\FlightBooking;
use App\Models\FlightPassenger;
use App\Notifications\BookingConfirmed;
use App\Services\AmadeusFlightService;
use App\Traits\BookingConfirmationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class FlightBookingController extends Controller
{
    use BookingConfirmationTrait;


    public function store(Request $request, AmadeusFlightService $amadeus)
    {
        $payload = $request->input('payload');
        $response = $amadeus->bookFlight($payload);
        $data = $payload;

        $offer = $data['flightOffers'][0];
        $segments = $offer['itineraries'][0]['segments'];
        $firstSegment = $segments[0];
        $lastSegment = end($segments);

        $booking = FlightBooking::create([
            'order_id'       => $offer['id'],
            'reference' => $data['associatedRecords'][0]['reference'] ?? strtoupper('TRV-' . \Str::random(6)),
            'email'          => $request->input('email'),
            'status'         => 'pending',
            'total_price'    => $offer['price']['grandTotal'],
            'currency'       => $offer['price']['currency'] ?? 'EUR',
            'origin'         => $firstSegment['departure']['iataCode'],
            'destination'    => $lastSegment['arrival']['iataCode'],
            'departure_time' => $firstSegment['departure']['at'],
            'arrival_time'   => $lastSegment['arrival']['at'],
            'airline'        => $firstSegment['carrierCode'],
            'segments_count' => count($segments),
            'data'           => $data,
        ]);

        return response()->json([
            'message' => 'Temporary booking created, proceed to payment.',
            'booking' => $booking
        ]);
    }


    public function confirm($bookingId)
    {
        $booking = FlightBooking::findOrFail($bookingId);

        if ($this->confirmBooking($booking)) {
            return response()->json(['message' => 'Booking confirmed and emails sent.']);
        }

        return response()->json(['message' => 'Booking must be paid before confirmation.'], 400);
    }


    public function cancel($bookingId)
    {
        $booking = FlightBooking::findOrFail($bookingId);

        $this->cancelBooking($booking);

        return response()->json(['message' => 'Booking canceled successfully.']);
    }
}
