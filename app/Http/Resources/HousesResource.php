<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HousesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'user_id'=> $this->user_id,
            'title'=> $this->title,
            'location'=> $this->location,
            'description'=> $this->description,
            'price_per_night'=> $this->price_per_night,
            'amenities' =>$this->amenities,
        ];
    }
}
