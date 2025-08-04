<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FlightPassenger extends Model
{
    use Notifiable;

    use HasFactory;
    protected $fillable = [
        'booking_id',
        'first_name',
        'last_name',
        'traveler_id',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'passport_number',
        'passport_expiry',
        'nationality',
        'amadeus_id',
    ];
    public function booking()
    {
        return $this->belongsTo(FlightBooking::class, 'booking_id');
    }


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
