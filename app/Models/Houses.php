<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Houses extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price_per_night',
        'amenities',
        'location',
    ];
}
