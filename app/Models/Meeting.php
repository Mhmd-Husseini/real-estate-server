<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'buyer_id', 
        'seller_id', 
        'property_id', 
        'requested_date1', 
        'requested_date2', 
        'requested_date3', 
        'status',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
