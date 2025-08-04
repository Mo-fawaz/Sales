<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'city_id',
        'description',
        'phone',
        'cuisine_type',
        'opening_hours',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
}
