<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class properties extends Model
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
}
