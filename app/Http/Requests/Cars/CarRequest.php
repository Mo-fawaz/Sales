<?php

namespace App\Http\Requests\Cars;

use Illuminate\Foundation\Http\FormRequest;
class CarRequest extends FormRequest {
    public function authorize(){ return true; }
    public function rules() {
        return [
            'brand'       => 'required|string|max:100',
            'model'       => 'required|string|max:100',
            'year'        => 'required|integer|min:1900|max:' . date('Y'),
            'type'        => 'required|string|max:50', // Sedan, SUV...
            'is_private'  => 'required|boolean',
            'is_taxi'     => 'required|boolean',
            'images'      => 'required|array|min:1',
            'images.*'    => 'image|max:2048',
            'pricing_type' => 'required|in:per_day,per_hour,per_km',
            'price'        => 'required|numeric|min:0',
            'city'     => 'required|string|max:100',
            'location' => 'required|string|max:255',

        ];
    }
}
