<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Flight extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'airline',
        'flight_number',
        'origin_city',
        'destination_city',
        'departure_date',
        'departure_time',
        'arrival_time',
        'available_seats',
        'price',
    ];

    public function bookings()
    {
        return $this->hasMany(FlightBooking::class);
    }
}
