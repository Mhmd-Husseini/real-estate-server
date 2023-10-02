<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id', 
        'rooms_nb', 
        'balconies_nb', 
        'bathrooms_nb', 
        'garages_nb',
    ];

    public function property()
    {
    return $this->belongsTo(Property::class);
    }
}
