<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model {
    protected $fillable = ['car_id','user_id','start_date','end_date','pickup_location','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

}
