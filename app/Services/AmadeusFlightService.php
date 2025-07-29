<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AmadeusFlightService
{
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = env('AMADEUS_CLIENT_ID');
        $this->clientSecret = env('AMADEUS_CLIENT_SECRET');
        $this->authenticate();
        $this->accessToken = $this->getAccessToken();
    }
    private function getAccessToken(): string
    {
        $response = Http::asForm()->post(config('services.amadeus.base_url') . '/v1/security/oauth2/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => config('services.amadeus.key'),
            'client_secret' => config('services.amadeus.secret'),
        ]);

        return $response->json()['access_token'];
    }
    public function bookFlight(array $payload): array
    {
        $response = Http::withToken($this->accessToken)
            ->post(config('services.amadeus.base_url') . '/v1/booking/flight-orders', $payload);

        return $response->json();
    }


    private function authenticate()
    {
        $response = Http::asForm()->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        $this->accessToken = $response->json()['access_token'];
    }

    public function searchFlights($originLocationCode, $destinationLocationCode, $departureDate, $adults = 1, $cabin = null)
    {
        $url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';

        $query = [
            'originLocationCode'      => $originLocationCode,
            'destinationLocationCode' => $destinationLocationCode,
            'departureDate'           => $departureDate,
            'adults'                  => $adults,
            'nonStop'                 => request()->get('nonStop', 'false'),
            'max'                     => 5,
        ];

        if ($cabin !== null) {
            $query['travelClass'] = $cabin;
        }

        $response = Http::withToken($this->accessToken)->get($url, $query);

        return $response->json();
    }




    public function simplifyFlightData($data)
    {
        $results = [];

        foreach ($data['data'] as $flight) {
            $itinerary = $flight['itineraries'][0];
            $segments = [];

            foreach ($itinerary['segments'] as $segment) {
                $segments[] = [
                    'from'      => $segment['departure']['iataCode'],
                    'to'        => $segment['arrival']['iataCode'],
                    'departure' => $segment['departure']['at'],
                    'arrival'   => $segment['arrival']['at'],
                ];
            }

            $results[] = [
                'id'            => $flight['id'],
                'origin'        => $itinerary['segments'][0]['departure']['iataCode'],
                'destination'   => end($itinerary['segments'])['arrival']['iataCode'],
                'departure_time' => $itinerary['segments'][0]['departure']['at'],
                'arrival_time'  => end($itinerary['segments'])['arrival']['at'],
                'duration'      => $itinerary['duration'],
                'airline'       => $flight['validatingAirlineCodes'][0] ?? '',
                'stops'         => count($itinerary['segments']) - 1,
                'price'         => $flight['price']['grandTotal'] . ' ' . $flight['price']['currency'],
                'segments'      => $segments,
            ];
        }

        return $results;
    }


    // For Check Pricing before Confirm booking :
    public function priceFlightOffer(array $flightOffer)
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';

        $response = Http::withToken($this->accessToken)
            ->post($url, [
                'data' => [
                    'type' => 'flight-offers-pricing',
                    'flightOffers' => [$flightOffer],
                ]
            ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => $response->json()
            ];
        }

        return [
            'success' => true,
            'priced_offer' => $response->json()
        ];
    }

    // Confirm Booking :
    public function createFlightOrder(array $flightOffer, array $travelers, array $payments)
    {
        $url = 'https://test.api.amadeus.com/v1/booking/flight-orders';

        $payload = [
            'data' => [
                'type' => 'flight-order',
                'flightOffers' => [$flightOffer],
                'travelers' => $travelers,
                'payments' => $payments,
            ]
        ];

        $response = Http::withToken($this->accessToken)
            ->post($url, $payload);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => $response->json()
            ];
        }

        return [
            'success' => true,
            'order' => $response->json()
        ];
    }
}
