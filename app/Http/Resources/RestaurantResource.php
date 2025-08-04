<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'cuisine_type' => $this->cuisine_type,
            'opening_hours' => $this->opening_hours,
            'images' => $this->images->map(function ($image) {
                return asset('storage/Restaurant/' . $image->filename);
            }),
        ];
    }
}
