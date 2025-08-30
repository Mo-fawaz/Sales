<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxiRequest extends Model {
    protected $fillable = ['user_id','car_id','type','pickup_location','dropoff_location','hours'];
    public function car() { return $this->belongsTo(Car::class); }
    public function user() { return $this->belongsTo(User::class); }
}
