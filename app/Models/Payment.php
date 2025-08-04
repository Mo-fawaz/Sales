<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'flight_booking_id',
        'charge_id',
        'transaction_id',
        'amount',
        'card_id',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'postal_code',
    ];

    public function booking()
    {
        return $this->belongsTo(FlightBooking::class, 'flight_booking_id');
    }
}
