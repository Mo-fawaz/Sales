<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Services\AmadeusFlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function searchFlightsWithinWeek(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'departure_date' => 'required|date',
            'adults' => 'required|integer|min:1'
        ]);

        $origin = strtoupper($request->origin);
        $destination = strtoupper($request->destination);
        $startDate = $request->departure_date;
        $adults = $request->adults;

        $amadeusService = new \App\Services\AmadeusFlightService();
        $allFlights = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = date('Y-m-d', strtotime($startDate . " +{$i} days"));

            $response = $amadeusService->searchFlights($origin, $destination, $currentDate, $adults);

            if (isset($response['data']) && is_array($response['data'])) {
                foreach ($response['data'] as $flight) {
                    // أضف تاريخ الرحلة حتى يكون واضح بأي يوم
                    $flight['searched_date'] = $currentDate;
                    $allFlights[] = $flight;
                }
            }
        }

        if (empty($allFlights)) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد رحلات متوفرة خلال هذا الأسبوع.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'flights' => $allFlights
        ]);
    }


    public function search(Request $request, AmadeusFlightService $amadeus)
    {
        $request->validate([
            'originLocationCode' => 'required|string|size:3',
            'destinationLocationCode' => 'required|string|size:3',
            'departureDate' => 'required|date',
            'adults' => 'required|integer|min:1|max:9',
            'cabin' => 'nullable|string|in:ECONOMY,BUSINESS,FIRST'
        ]);

        $cabin = $request->input('cabin', null);


        $rawData = $amadeus->searchFlights(
            $request->originLocationCode,
            $request->destinationLocationCode,
            $request->departureDate,
            $request->adults,
            $cabin
        );

        // $flights = $amadeus->simplifyFlightData($rawData);

        return response()->json([
            'success' => true,
            'flights' => $rawData
        ]);
    }

    // Price booking before Confirm
    public function confirmFlightPrice(Request $request, AmadeusFlightService $amadeus)
    {
        $validated = $request->validate([
            'flightOffer' => 'required|array',
        ]);

        $result = $amadeus->priceFlightOffer($validated['flightOffer']);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تأكيد السعر',
                'error' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'priced_offer' => $result['priced_offer']
        ]);
    }

    // Confirm Booking :
    public function bookFlight(Request $request, AmadeusFlightService $amadeus)
    {
        $validated = $request->validate([
            'flightOffer' => 'required|array',
            'travelers' => 'required|array',
            'payments' => 'required|array',
        ]);

        $result = $amadeus->createFlightOrder(
            $validated['flightOffer'],
            $validated['travelers'],
            $validated['payments']
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إجراء الحجز',
                'error' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'order' => $result['order']
        ]);
    }
}
