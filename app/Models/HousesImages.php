<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousesImages extends Model
{
    //
     protected $fillable = [
        'house_id',
        'path',
    ];
    public function house()
    {
        return $this->belongsTo(Houses::class);
    }

}

