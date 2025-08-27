<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarsReview extends Model
{
    protected $fillable = ['car_id','user_id','rating','comment'];
    public function car() { return $this->belongsTo(Car::class); }
    public function user() { return $this->belongsTo(User::class); }
}
