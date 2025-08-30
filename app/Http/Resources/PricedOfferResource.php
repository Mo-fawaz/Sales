<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricedOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'offer_id' => $this->offer_id,
            'carrier_code' => $this->carrier_code,
            'price' => $this->price,
            'currency' => $this->currency,
            'departure_airport' => $this->departure_airport,
            'arrival_airport' => $this->arrival_airport,
            'departure_time' => $this->departure_time->toIso8601String(),
            'arrival_time' => $this->arrival_time->toIso8601String(),
            'duration' => $this->duration,
            'stops' => $this->stops,
            //'all_details' => $this->all_details,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
