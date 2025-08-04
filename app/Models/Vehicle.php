<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Vehicle extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'type',
        'seats',
        'price_per_trip',
        'location',
        'self_drive',
        'available_from',
        'available_to',
    ];

    public function bookings()
    {
        return $this->hasMany(VehicleBooking::class);
    }
}
