<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlightBooking;
use App\Models\FlightPassenger;
use Illuminate\Http\Request;

class FlightPassengerController extends Controller
{
    public function storePassengers(Request $request)
    {
        $request->validate([
            'data.travelers' => 'required|array',
            'data.travelers.*.id' => 'required',
            'data.travelers.*.name.firstName' => 'required|string',
            'data.travelers.*.name.lastName' => 'required|string',
            'data.travelers.*.dateOfBirth' => 'required|date',
            'data.travelers.*.gender' => 'required|string',
            'data.travelers.*.documents' => 'required|array',
            'data.travelers.*.documents.*.number' => 'required|string',
            'data.travelers.*.documents.*.expiryDate' => 'required|date',
            'data.travelers.*.documents.*.issuanceCountry' => 'required|string',
            'data.travelers.*.documents.*.nationality' => 'required|string',
            'data.travelers.*.documents.*.documentType' => 'required|string',
            'data.travelers.*.contact.emailAddress' => 'required|email',
            'booking_id' => 'required|exists:flight_bookings,id',
        ]);
        $booking = FlightBooking::findOrFail($request->booking_id);

        if ($booking->status !== 'paid') {
            return response()->json(['error' => 'Booking payment is not confirmed'], 403);
        }


        foreach ($request->input('data.travelers') as $traveler) {
            FlightPassenger::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'traveler_id' => $traveler['id'],
                ],
                [
                    'first_name' => $traveler['name']['firstName'],
                    'last_name' => $traveler['name']['lastName'],
                    'date_of_birth' => $traveler['dateOfBirth'],
                    'gender' => $traveler['gender'],
                    'email' => $traveler['contact']['emailAddress'],
                    'phone' => $traveler['contact']['phones'][0]['number'],
                    'passport_number' => $traveler['documents'][0]['number'],  // مهم
                    'passport_expiry' => $traveler['documents'][0]['expiryDate'],
                    'nationality' => $traveler['documents'][0]['nationality'],
                    'amadeus_id' => $traveler['id'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Passengers stored successfully',
            'booking_id' => $booking->id,
        ]);
    }
}
