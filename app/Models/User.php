<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'passport',
        'phone',
        'nationality'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bookings()
    {
        return $this->hasMany(FlightBooking::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Get All Favorites :
    public function allFavorites()
    {
        return $this->favorites()->with('favoritable')->get();
    }


    public function favoriteHotels()
    {
        return $this->favorites()->where('favoritable_type', \App\Models\Hotel::class);
    }
    public function favoriteRestaurant()
    {
        return $this->favorites()->where('favoritable_type', \App\Models\Restaurant::class);
    }
    public function favoriteTouristPlace()
    {
        return $this->favorites()->where('favoritable_type', \App\Models\TouristPlace::class);
    }
}
