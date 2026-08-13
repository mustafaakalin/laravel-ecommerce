<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'value' => 'float',
    ];


    protected $dates = ['starts_at', 'expires_at'];

    public function isValid()
    {
        return $this->is_active &&
            (!$this->starts_at || $this->starts_at <= now()) &&
            (!$this->expires_at || $this->expires_at >= now()) &&
            (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    // public function applyDiscount($total)
    // {
    //     if ($this->type === 'fixed') {
    //         return min($this->value, $total); // Fixed indirim miktarını toplam fiyattan düşür
    //     } elseif ($this->type === 'percentage') {
    //         return $total * ($this->value / 100); // Yüzde indirim miktarını hesapla
    //     }
    //     return 0;
    // }
    public function applyDiscount($total)
    {
        if ($this->type === 'fixed') {
            $discount = min($this->value, $total); // Sabit indirim miktarını hesapla
            return $discount; // İndirim miktarını döndür
        } elseif ($this->type === 'percentage') {
            $discount = $total * ($this->value / 100); // Yüzde indirim miktarını hesapla
            return $discount; // İndirim miktarını döndür
        }
        return 0; // İndirim yoksa 0 döndür
    }
}
