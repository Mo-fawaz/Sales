<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class VehicleBooking extends Model
{
    use HasFactory, HasApiTokens;

    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'pickup_location',
        'dropoff_location',
        'pickup_time',
        'number_of_passengers',
        'status',
        'self_drive',
        'driver_license_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
