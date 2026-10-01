<?php

namespace App\Models;

use App\Support\CacheKeys;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ShipmentDiscount extends Model
{
    protected $fillable = [
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function cachedPrice(): float
    {
        return (float) Cache::flexible(
            CacheKeys::shipmentDiscount(),
            [300, 3600],
            static fn () => self::query()->value('price') ?? 0
        );
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget(CacheKeys::shipmentDiscount());
        });

        static::deleted(function (): void {
            Cache::forget(CacheKeys::shipmentDiscount());
        });
    }
}
