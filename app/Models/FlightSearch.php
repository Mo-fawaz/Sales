<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSearch extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'adults',
        'children',
        'travel_class',
        'search_id'
    ];

    public function offers()
    {
        return $this->hasMany(FlightOffer::class);
    }
    // FlightSearch model
    // public function offers()
    // {
    //     return $this->hasMany(FlightOffer::class, 'flight_search_id');
    // }
}
