<?php

namespace App\Http\Requests\Cars;

use Illuminate\Foundation\Http\FormRequest;

class CarUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand'       => 'sometimes|string|max:100',
            'model'       => 'sometimes|string|max:100',
            'year'        => 'sometimes|integer|min:1900|max:' . date('Y'),
            'type'        => 'sometimes|string|max:50',
            'is_private'  => 'sometimes|boolean',
            'is_taxi'     => 'sometimes|boolean',
            'images'      => 'sometimes|array',
            'images.*'    => 'image|max:2048',
            'pricing_type' => 'sometimes|in:per_day,per_hour,per_km',
            'price'        => 'sometimes|numeric|min:0',

        ];
    }
}
