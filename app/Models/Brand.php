<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name', 'slug', 'logo', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // Aktif ürünleri filtrelemek için scope tanımlayın
    public function scopeActiveProductsCount($query)
    {
        return $query->withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }]);
    }


    public function scopeActiveProducts($query)
    {
        return $query->whereHas('products', function ($query) {
            $query->where('is_active', true);
        });
    }



    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('public');
    }

    // Helper method for getting media url
    public function getFirstMediaUrl($collection = 'default', $conversion = '')
    {
        $media = $this->getFirstMedia($collection);
        
        if ($media) {
            return $media->getUrl($conversion);
        }

        // Return a default logo if no media exists
        return asset('default_brand_image.png');
    }




}
