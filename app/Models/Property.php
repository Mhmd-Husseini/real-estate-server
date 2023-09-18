<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'city_id', 
        'type',
        'title', 'description', 
        'status', 
        'price', 
        'area', 
        'address', 'latitude', 'longitude',
        'img1', 'img2', 'img3', 
    ];

    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function home()
    {
        return $this->hasOne(Home::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'property_id');
    }
}
