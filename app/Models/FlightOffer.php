<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightOffer extends Model
{
    protected $casts = [
        'all_details' => 'array',
    ];

    protected $fillable = [
        'flight_search_id',
        'offer_id',
        'carrier_code',
        'price',
        'currency',
        'departure_airport',
        'arrival_airport',
        'departure_time',
        'arrival_time',
        'duration',
        'all_details',
        'stops'
    ];

    public function search()
    {
        return $this->belongsTo(FlightSearch::class);
    }
}
