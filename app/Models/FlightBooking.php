<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class FlightBooking extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'order_id',
        'reference',
        'email',
        'status',
        'total_price',
        'currency',
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'airline',
        'segments_count',
        'data',
        'ticket_number',
    ];

    protected $casts = [
        'data' => 'array',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function passengers()
    {
        return $this->hasMany(FlightPassenger::class , "booking_id");
    }
}
