<?php

namespace App\Http\Requests\Cars;

use Illuminate\Foundation\Http\FormRequest;

class CarsReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(){ return true; }
    public function rules() {
        return [
            'car_id'=>'required|exists:cars,id',
            'rating'=>'required|integer|min:1|max:5',
            'comment'=>'nullable|string'
        ];
    }
}
