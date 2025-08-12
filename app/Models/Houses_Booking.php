<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Houses_Booking extends Model
{
    //
    protected $fillable = [
        'user_id',
        'house_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
    ];
    protected $table = 'houses_booking';
}
