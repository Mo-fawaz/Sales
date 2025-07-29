<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AccommodationBooking extends Model
{
    use HasFactory, HasApiTokens;

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
