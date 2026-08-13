<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDiscount extends Model
{
    protected $fillable = [
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

}
