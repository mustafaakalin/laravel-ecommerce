<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'discount_type', 'discount_value',
        'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
        'discount_value' => 'decimal:2'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }


    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date->greaterThan($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lessThan($now)) {
            return false;
        }

        return true;
    }

    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (!$campaign->slug) {
                $campaign->slug = Str::slug($campaign->name);
            }
        });
    }

    // for mobile api 
    public function getFirstMediaUrl($collection = 'default', $conversion = '')
    {
        return optional($this->getFirstMedia($collection))->getUrl($conversion);
    }

    //// for spatie media library , if you dont know , check product model and resource
//     public function registerMediaCollections(): void
// {
//     $this->addMediaCollection('images')
//         ->useDisk('public');
// }


}
