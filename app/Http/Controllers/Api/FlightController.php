<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlightDetailsResource;
use App\Http\Resources\FlightSearchResource;
use App\Http\Resources\PricedOfferResource;
use App\Models\Flight;
use App\Models\FlightOffer;
use App\Models\FlightSearch;
use App\Models\FlightSearchResult;
use App\Services\AmadeusFlightService;
use Carbon\Carbon;
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
            'originLocationCode'      => 'required|string|size:3',
            'destinationLocationCode' => 'required|string|size:3',
            'departureDate'           => 'required|date',
            'returnDate'              => 'nullable|date',
            'adults'                  => 'required|integer|min:1|max:9',
            'children'                => 'nullable|integer|min:0|max:9',
            'cabin'                   => 'nullable|string|in:ECONOMY,BUSINESS,FIRST',
        ]);

        try {
            $rawData = $amadeus->searchFlights(
                $request->originLocationCode,
                $request->destinationLocationCode,
                $request->departureDate,
                $request->adults,
                $request->input('cabin'),
                $request->input('children', 0),
                $request->input('returnDate')
            );

            if (empty($rawData['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No flight data found.'
                ], 404);
            }

            $search = FlightSearch::create([
                'origin'        => $request->originLocationCode,
                'destination'   => $request->destinationLocationCode,
                'departure_date' => $request->departureDate,
                'return_date'   => $request->returnDate,
                'adults'        => $request->adults,
                'children'      => $request->input('children', 0),
                'travel_class'  => $request->input('cabin'),
                'search_id'     => $rawData['meta']['searchId'] ?? uniqid('search_', true),
            ]);

            // foreach ($rawData['data'] as $flight) {
            //     $segmentsData = [];
            //     foreach ($flight['itineraries'][0]['segments'] as $segment) {
            //         $segmentsData[] = [
            //             'carrierCode'   => $segment['carrierCode'],
            //             'departure'     => $segment['departure']['iataCode'],
            //             'arrival'       => $segment['arrival']['iataCode'],
            //             'departureTime' => $segment['departure']['at'],
            //             'arrivalTime'   => $segment['arrival']['at'],
            //         ];
            //     }

            //     $search->offers()->create([
            //         'offer_id'        => $flight['id'],
            //         'carrier_code'    => $segmentsData[0]['carrierCode'],
            //         'price'           => $flight['price']['total'],
            //         'currency'        => $flight['price']['currency'],
            //         'departure_airport' => $segmentsData[0]['departure'],
            //         'arrival_airport'   => end($segmentsData)['arrival'],
            //         'departure_time'  => $segmentsData[0]['departureTime'],
            //         'arrival_time'    => end($segmentsData)['arrivalTime'],
            //         'duration'        => $flight['itineraries'][0]['duration'],
            //         'stops'           => count($segmentsData) - 1,
            //         'fare_basis'         => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['fareBasis'] ?? null,
            //         'branded_fare'       => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['brandedFare'] ?? null,
            //         'branded_fare_label' => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['brandedFareLabel'] ?? null,
            //         'cabin_bags'         => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['includedCabinBags']['quantity'] ?? null,
            //         'checked_bags_weight' => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['includedCheckedBags']['weight'] ?? null,
            //         'checked_bags_unit'  => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['includedCheckedBags']['weightUnit'] ?? null,
            //         'last_ticketing_date' => $flight['lastTicketingDate'] ?? null,
            //         'all_details'        => $flight,
            //     ]);
            // }

            foreach ($rawData['data'] as $flight) {
                $segmentsData = [];
                foreach ($flight['itineraries'][0]['segments'] as $segment) {
                    $segmentsData[] = [
                        'carrierCode'   => $segment['carrierCode'],
                        'departure'     => $segment['departure']['iataCode'],
                        'arrival'       => $segment['arrival']['iataCode'],
                        'departureTime' => $segment['departure']['at'],
                        'arrivalTime'   => $segment['arrival']['at'],
                    ];
                }
                $fareDetails = $flight['travelerPricings'][0]['fareDetailsBySegment'][0] ?? null;
                $cabinBags = $fareDetails['includedCabinBags']['quantity'] ?? null;
                $checkedBagsWeight = $fareDetails['includedCheckedBags']['weight'] ?? null;
                $checkedBagsUnit = $fareDetails['includedCheckedBags']['weightUnit'] ?? null;
                $lastTicketingDate = $flight['lastTicketingDate'] ?? null;
                $search->offers()->create([
                    'offer_id'            => $flight['id'],
                    'carrier_code'        => $segmentsData[0]['carrierCode'],
                    'price'               => $flight['price']['total'],
                    'currency'            => $flight['price']['currency'],
                    'departure_airport'   => $segmentsData[0]['departure'],
                    'arrival_airport'     => end($segmentsData)['arrival'],
                    'departure_time'      => $segmentsData[0]['departureTime'],
                    'arrival_time'        => end($segmentsData)['arrivalTime'],
                    'duration'            => $flight['itineraries'][0]['duration'],
                    'stops'               => count($segmentsData) - 1,
                    'fare_basis'          => $fareDetails['fareBasis'] ?? null,
                    'branded_fare'        => $fareDetails['brandedFare'] ?? null,
                    'branded_fare_label'  => $fareDetails['brandedFareLabel'] ?? null,
                    'cabin_bags'          => $cabinBags,
                    'checked_bags_weight' => $checkedBagsWeight,
                    'checked_bags_unit'   => $checkedBagsUnit,
                    'last_ticketing_date' => $lastTicketingDate,
                    'all_details'         => $flight,
                ]);
            }


            return new FlightSearchResource($search->load('offers'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث عن الرحلات.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getFlightDetails(Request $request)
    {

        $search = FlightSearch::with('offers')->where('search_id', $request->search_id)->first();
        if (!$search) {
            return response()->json([
                'success' => false,
                'message' => 'Flight search not found.'
            ], 404);
        }
        $offer = $search->offers()->where('offer_id', (int) $request->offer_id)->first();
        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Flight offer not found.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'flight' => $offer
            // 'flight' => new FlightDetailsResource($offer)
        ]);
    }

    public function confirmFlightPrice(Request $request, AmadeusFlightService $amadeus)
    {
        dd($request->all_details);
        $validated = $request->validate([
            'search_id' => 'required|string',
            'offer_id' => 'required|string',
        ]);

        $offer = FlightOffer::where('flight_search_id', $validated['search_id'])
            ->where('offer_id', $validated['offer_id'])
            ->first();

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Flight offer not found locally. Please search again.'
            ], 404);
        }

        //--------------------------------------------------------------------------
        //  The Fix
        //  The 'all_details' attribute from the database is already a JSON string.
        //  You must decode it before sending it to the API.
        //--------------------------------------------------------------------------
        $allDetails = json_decode($offer->all_details, true);

        // Now, send the PHP array directly.
        // The Amadeus service will handle encoding it to JSON internally.
        $result = $amadeus->priceFlightOffer($allDetails);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm the price.',
                'error' => $result['error']
            ], 400);
        }

        $pricedOfferData = $result['priced_offer']['data'] ?? null;

        if (
            $pricedOfferData &&
            isset($pricedOfferData['price']['total']) &&
            isset($pricedOfferData['price']['currency'])
        ) {
            $offer->price = $pricedOfferData['price']['total'];
            $offer->currency = $pricedOfferData['price']['currency'];
            $offer->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pricing data received from Amadeus.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'priced_offer' => new PricedOfferResource($offer),
        ]);
    }


    // public function confirmFlightPrice(Request $request, AmadeusFlightService $amadeus)
    // {
    //     $validated = $request->validate([
    //         'search_id' => 'required|string',
    //         'offer_id' => 'required|string',
    //     ]);
    //     $offer = FlightOffer::where('flight_search_id', $validated['search_id'])
    //         ->where('offer_id', $validated['offer_id'])
    //         ->first();

    //     if (!$offer) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Flight offer not found locally. Please search again.'
    //         ], 404);
    //     }
    //     $result = $amadeus->priceFlightOffer($offer->all_details);

    //     if (!$result['success']) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to confirm the price.',
    //             'error' => $result['error']
    //         ], 400);
    //     }
    //     $pricedOfferData = $result['priced_offer']['data'] ?? null;
    //     if (
    //         $pricedOfferData &&
    //         isset($pricedOfferData['price']['total']) &&
    //         isset($pricedOfferData['price']['currency'])
    //     ) {
    //         $offer->price = $pricedOfferData['price']['total'];
    //         $offer->currency = $pricedOfferData['price']['currency'];
    //         $offer->save();
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid pricing data received from Amadeus.',
    //         ], 400);
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'priced_offer' => new PricedOfferResource($offer),
    //     ]);
    // }




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
