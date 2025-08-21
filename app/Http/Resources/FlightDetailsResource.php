<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightDetailsResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'offer_id'           => $this->offer_id,
            'carrier_code'       => $this->carrier_code,
            'departure_airport'  => $this->departure_airport,
            'arrival_airport'    => $this->arrival_airport,
            'departure_time'     => $this->departure_time,
            'arrival_time'       => $this->arrival_time,
            'duration'           => $this->duration,
            'stops'              => $this->stops,
            'price'              => $this->price,
            'currency'           => $this->currency,
            'fare_basis'         => $this->fare_basis,
            'branded_fare'       => $this->branded_fare,
            'branded_fare_label' => $this->branded_fare_label,
            'cabin_bags'         => $this->cabin_bags,
            'checked_bags_weight' => $this->checked_bags_weight,
            'checked_bags_unit'  => $this->checked_bags_unit,
            'last_ticketing_date' => $this->last_ticketing_date,
            'segments'           => json_decode($this->segments),
            'seat_types'         => $this->seat_types,
            'meal_options'       => $this->meal_options,
            'allowed_bags_quantity' => $this->allowed_bags_quantity,
            'paid_bags_quantity' => $this->paid_bags_quantity,
            'expires_at'         => $this->expires_at,
        ];
    }
}
