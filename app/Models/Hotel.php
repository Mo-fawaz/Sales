<?php

namespace App\Models;

use App\Models\Image;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\City;

class Hotel extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'location', 'description', 'phone', 'stars', 'city_id','price_per_night', 'images', 'location', 'amenities'];


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
