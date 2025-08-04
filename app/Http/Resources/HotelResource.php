<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
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
            'name' => $this->name,
            'location' => $this->location,
            'city' => $this->city->name ?? null,
            'description' => $this->description,
            'phone' => $this->phone,
            'stars' => $this->stars,
            'images' => $this->images->map(function ($image) {
                return asset('storage/Hotel/' . $image->filename);
            }),
        ];
    }
}
