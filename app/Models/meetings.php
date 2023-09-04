<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class meetings extends Model
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
}
