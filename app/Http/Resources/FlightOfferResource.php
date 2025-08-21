<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightOfferResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'offer_id'        => $this->offer_id,
            'carrier_code'    => $this->carrier_code,
            'price'           => $this->price,
            'currency'        => $this->currency,
            'departure_airport' => $this->departure_airport,
            'arrival_airport'   => $this->arrival_airport,
            'departure_time'  => $this->departure_time,
            'arrival_time'    => $this->arrival_time,
            'duration'        => $this->duration,
            'stops'           => $this->stops,
        ];
    }
}
