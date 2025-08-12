<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Houses extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price_per_night',
        'amenities',
        'location',
        'image',
    ];
   protected $casts = [
    'amenities' => 'array', // يحول JSON من/إلى array تلقائيًا
    ];
    use HasFactory; 
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
