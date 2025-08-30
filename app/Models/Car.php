<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model {
    protected $fillable = [
        'owner_id','brand','model','year','type','is_taxi',
        'pickup_location','approved','pricing_type','price','city','location'
    ];


    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id');
    }
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
    public function reviews()
    {
        return $this->hasMany(CarsReview::class);
    }
    public function images()
    {
        return $this->hasMany(CarImage::class);
    }
}

