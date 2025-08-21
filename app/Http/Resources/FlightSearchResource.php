<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightSearchResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'origin'        => $this->origin,
            'destination'   => $this->destination,
            'departure_date' => $this->departure_date,
            'return_date'   => $this->return_date,
            'adults'        => $this->adults,
            'children'      => $this->children,
            'travel_class'  => $this->travel_class,
            'search_id'     => $this->search_id,
            'offers'        => FlightOfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}
