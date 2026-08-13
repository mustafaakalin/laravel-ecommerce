<?php

namespace App\Models;


use App\Models\OrderItem;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CampaignProduct;


class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category_id',
        'brand_id',
        'old_price',
        'is_active',
        'is_featured',
        'is_new',
        'discount',
        'rating',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'search_keywords',
        'is_digital',
        'view_count',
        'specifications',
        'sku',
        'is_free_shipping'
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_digital' => 'boolean',
        'is_free_shipping' => 'boolean',
    ];

    // İlişkiler

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function hasBeenPurchasedBy(User $user): bool
    {
        return OrderItem::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'completed');
        })
            ->where('product_id', $this->id)
            ->exists();
    }

    public function isLikedBy($user)
    {
        if (!$user)
            return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function campaignProduct()
    {
        return $this->belongsTo(CampaignProduct::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Metods
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    // public function activeCampaign()
    // {
    //     return $this->campaigns()
    //         ->where('is_active', true)
    //         ->where('start_date', '<=', now())
    //         ->where('end_date', '>=', now())
    //         ->orderBy('discount_value', 'desc')  // Get the best discount if multiple campaigns exist
    //         ->first();
    // }

    // public function activeCampaign2()
    // {
    //     return $this->belongsToMany(Campaign::class)
    //         ->where('is_active', true)
    //         ->where('start_date', '<=', now())
    //         ->where('end_date', '>=', now())
    //         ->orderBy('created_at', 'desc')
    //         ->limit(1);
    // }


    // public function activeCampaign()
    // {
    //     return $this->belongsToMany(Campaign::class)
    //         ->where('is_active', true)
    //         ->where('start_date', '<=', now())
    //         ->where('end_date', '>=', now())
    //         ->orderBy('created_at', 'desc')
    //         ->limit(1);
    // }
    public function activeCampaign()
    {
        return $this->belongsToMany(Campaign::class)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first(); // Changed to first()
    }

public function isCampaignProduct()
{
    return $this->campaignProduct()
        ->where('campaign_id', $this->activeCampaign()->id)
        ->where('product_id', '=', $this->id)
        ->exists();
}


    public function getCurrentPrice()
    {
        // $campaign = $this->activeCampaign();

        // if ($campaign) {
        //     if ($campaign->discount_type === 'percentage') {
        //         return $this->price * (1 - ($campaign->discount_value / 100));
        //     } elseif ($campaign->discount_type === 'fixed') {
        //         return max(0, $this->price - $campaign->discount_value);
        //     }
        // }

        return $this->discount ?
            $this->price - ($this->price * $this->discount / 100) :
            $this->price;
    }


    public function getCurrentPrice2()
    {
        try {
            $basePrice = $this->price;
            $finalPrice = $basePrice;

            // Check product discount
            if ($this->discount > 0) {
                $discountedPrice = $basePrice - ($basePrice * $this->discount / 100);
                // Ensure discounted price is not greater than base price
                $discountedPrice = min($basePrice, $discountedPrice);
                $finalPrice = min($finalPrice, $discountedPrice);
            }

            // Check active campaign discount
            $campaign = $this->activeCampaign();
            if ($campaign) {
                if ($campaign->discount_type === 'percentage') {
                    $campaignPrice = $basePrice * (1 - ($campaign->discount_value / 100));
                    // Ensure campaign price is not greater than base price
                    $campaignPrice = min($basePrice, $campaignPrice);
                    $finalPrice = min($finalPrice, $campaignPrice);
                } elseif ($campaign->discount_type === 'fixed') {
                    $campaignPrice = max(0, $basePrice - $campaign->discount_value);
                    // Ensure campaign price is not greater than base price
                    $campaignPrice = min($basePrice, $campaignPrice);
                    $finalPrice = min($finalPrice, $campaignPrice);
                }
            }

            // Final safety check to ensure discounted price never exceeds original price
            $finalPrice = min($basePrice, $finalPrice);

            return round($finalPrice, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating product price: ' . $e->getMessage());
            return $this->price;
        }
    }


    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function isStock()
    {
        if ($this->stock > 0) {
            return $this->stock;
        }
        return 0;
    }

    public function isInActive()
    {
        return $this->is_active = true;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
                $product->save();
            }
        });
    }



    // Add to Product.php
    public function getAvailableStockForUser($userId)
    {
        $cartQuantity = $this->cartItems()
            ->whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('quantity');

        return $this->stock - $cartQuantity;
    }

    public function hasSufficientStock($requestedQuantity, $userId)
    {
        return $this->getAvailableStockForUser($userId) >= $requestedQuantity;
    }


    public function relatedProducts()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id')
            ->where('id', '!=', $this->id)
            ->limit(4);
    }

    // for mobile api resource
    public function getFirstMediaUrl($collection = 'default', $conversion = '')
    {
        if ($this->images->isNotEmpty()) {
            return $this->images->first()->getFullUrl($collection, $conversion);
        }

        return asset('images/default_product_image.jpg');
    }

    public function averageRating()
    {
        return round($this->ratings()->avg('rating') ?? 0, 1);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');
    }

    // Add this relationship after your other relationships
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }





    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'category_id' => (int) $this->category_id,
            'brand_id' => (int) $this->brand_id,
            'old_price' => $this->old_price ? (float) $this->old_price : null,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'is_new' => (bool) $this->is_new,
            'discount' => (int) $this->discount,
            'rating' => (float) $this->rating,
            'is_digital' => (bool) $this->is_digital,
            'view_count' => (int) $this->view_count,
            'sku' => $this->sku,
            'tags' => $this->tags->pluck('name')->toArray(),
            'is_free_shipping' => (bool) $this->is_free_shipping,
            'created_at' => $this->created_at ? $this->created_at->timestamp : null,
            'images' => $this->images->pluck('image_path')->toArray(),
            'category_name' => $this->category ? $this->category->name : null,
            'brand_name' => $this->brand ? $this->brand->name : null,
        ];
    }


}
