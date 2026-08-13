<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function campaign()
    {
        return $this->product->activeCampaign();
    }

    public function getDiscountedPrice()
    {
        $basePrice = $this->product->getCurrentPrice();
        $campaign = $this->campaign();
        $productDiscount = $this->product->discount;

        try {
            // Calculate both discounts
            $productDiscountPrice = $productDiscount > 0 ?
                $basePrice * (1 - ($productDiscount / 100)) :
                $basePrice;

            $campaignPrice = $basePrice;
            if ($campaign) {
                if ($campaign->discount_type === 'percentage') {
                    $campaignPrice = $basePrice * (1 - ($campaign->discount_value / 100));
                } elseif ($campaign->discount_type === 'fixed') {
                    $campaignPrice = max(0, $basePrice - $campaign->discount_value);
                }
            }

            // Return the lowest price
            return round(min($productDiscountPrice, $campaignPrice), 2);
        } catch (\Exception $e) {
            Log::error('Price calculation error: ' . $e->getMessage());
            return $basePrice;
        }
    }

    public function getDiscountInfo()
    {
        $basePrice = $this->product->price;
        $campaign = $this->campaign();
        $productDiscount = $this->product->discount;

        $discounts = [];

        if ($productDiscount > 0) {
            $discounts['product'] = [
                'type' => 'percentage',
                'value' => $productDiscount,
                'final_price' => round($basePrice * (1 - ($productDiscount / 100)), 2)
            ];
        }

        if ($campaign) {
            $campaignPrice = $campaign->discount_type === 'percentage' ?
                round($basePrice * (1 - ($campaign->discount_value / 100)), 2) :
                round(max(0, $basePrice - $campaign->discount_value), 2);

            $discounts['campaign'] = [
                'type' => $campaign->discount_type,
                'value' => $campaign->discount_value,
                'final_price' => $campaignPrice,
                'name' => $campaign->name
            ];
        }

        return $discounts;
    }

    public function getBestDiscount()
    {
        $discounts = $this->getDiscountInfo();

        if (empty($discounts)) {
            return null;
        }

        return collect($discounts)->sortBy('final_price')->first();
    }

    public function getTotalPrice()
    {
        return round($this->quantity * $this->getDiscountedPrice(), 2);
    }



    public function calculatePrices(): array
    {
        try {
            // 1. Base product price
            $productPrice = $this->product->price;

            // 2. Product's own discount
            $productOwnDiscount = 0;
            $productFinalPrice = $productPrice;

            if ($this->product->discount > 0) {
                $productOwnDiscount = ($productPrice * $this->product->discount / 100);
                $productFinalPrice = $productPrice - $productOwnDiscount;

                Log::info('Product discount applied:', [
                    'product_id' => $this->product_id,
                    'original_price' => $productPrice,
                    'discount_percentage' => $this->product->discount,
                    'discount_amount' => $productOwnDiscount,
                    'price_after_discount' => $productFinalPrice
                ]);
            }

            // 3. Campaign discount if applicable
            $campaignDiscount = 0;
            if ($campaign = $this->product->activeCampaign()) {
                if ($campaign->isActive()) {
                    if ($campaign->discount_type === 'percentage') {
                        $campaignDiscount = $productFinalPrice * ($campaign->discount_value / 100);
                    } else {
                        $campaignDiscount = min($campaign->discount_value, $productFinalPrice);
                    }
                    $productFinalPrice = max(0, $productFinalPrice - $campaignDiscount);

                    Log::info('Campaign discount applied:', [
                        'product_id' => $this->product_id,
                        'campaign_id' => $campaign->id,
                        'discount_type' => $campaign->discount_type,
                        'discount_amount' => $campaignDiscount,
                        'price_after_campaign' => $productFinalPrice
                    ]);
                }
            }

            // 4. Calculate total for quantity
            $totalPrice = $productFinalPrice * $this->quantity;

            return [
                'original_price' => $productPrice,
                'product_discount' => [
                    'percentage' => $this->product->discount,
                    'amount' => $productOwnDiscount,
                ],
                'campaign_discount' => $campaign ? [
                    'name' => $campaign->name,
                    'type' => $campaign->discount_type,
                    'value' => $campaign->discount_value,
                    'amount' => $campaignDiscount,
                ] : null,
                'final_unit_price' => $productFinalPrice,
                'quantity' => $this->quantity,
                'total_price' => $totalPrice,
                'total_savings' => ($productPrice - $productFinalPrice) * $this->quantity
            ];
        } catch (\Exception $e) {
            Log::error('Price calculation error:', [
                'item_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }



    public function getOriginalPrice()
    {
        return $this->product->price;
    }

    public function getDiscountPercentage()
    {
        $originalPrice = $this->product->price;
        $finalPrice = $this->getDiscountedPrice();
        $discountPercentage = (($originalPrice - $finalPrice) / $originalPrice) * 100;

        return round($discountPercentage, 2);
    }
}
