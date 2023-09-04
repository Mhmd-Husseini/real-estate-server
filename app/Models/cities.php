<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cities extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
    ];

    public function properties()
    {
    return $this->hasMany(Property::class);
    }
}
