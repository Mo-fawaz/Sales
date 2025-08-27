<?php

namespace App\Http\Requests\Cars;

use Illuminate\Foundation\Http\FormRequest;

class TaxiRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(){ return true; }
    public function rules() {
        return [
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
            'passengers' => 'required|integer|min:1',
            'car_type' => 'nullable|string|max:50',
        ];
    }
}
