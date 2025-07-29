<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Hotel extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'location', 'description', 'phone', 'stars', 'city_id'];


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
