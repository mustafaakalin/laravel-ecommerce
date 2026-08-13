<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'credentials',
        'test_mode',
        'supported_currencies',
        'minimum_amount',
        'maximum_amount',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'test_mode' => 'boolean',
        'credentials' => 'encrypted:array',
        'supported_currencies' => 'array',
        'settings' => 'array'
    ];

    public function isAvailableForAmount($amount): bool
    {
        return $amount >= ($this->minimum_amount ?? 0) && 
               (!$this->maximum_amount || $amount <= $this->maximum_amount);
    }
}