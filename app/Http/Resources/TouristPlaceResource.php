<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TouristPlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'location'      => $this->location,
            'description'   => $this->description,
            'entry_fee'     => $this->entry_fee,
            'opening_hours' => $this->opening_hours,
            'phone'         => $this->phone,
            'city'          => $this->city->name ?? null,
            'images'        => $this->images->map(function ($img) {
                return asset('storage/TouristPlaces/' . $img->filename);
            }),
        ];
    }
}
