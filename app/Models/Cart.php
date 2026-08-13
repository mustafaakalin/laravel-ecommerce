<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'coupon_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Cart Toplam Tutarı
    public function getTotalPrice()
    {
        return $this->items->sum(function ($item) {
            $cartperproductprice = $item->quantity * $item->product->getCurrentPrice();


            return $cartperproductprice;
        });
    }

    // Cart Item Sayısı
    public function getTotalItems()
    {
        return $this->items->sum('quantity');
    }



    public function calculateTotalItems()
    {
        return $this->items->sum('quantity');
    }


    public function calculateTotalPrice(): float
    {
        try {
            if (auth()->user()->hasRole('admin')) {
                return $this->items->sum(function ($item) {
                    return $item->getTotalPrice();
                });
            }

            // if ($this->user_id === auth()->id()) {
            //     return $this->items->sum(function ($item) {
            //         $siteSetting = SiteSetting::first();
            //         $siteShipmentPrice = $siteSetting->site_shipment_price;

            //         if ($item->getTotalPrice() >= ShipmentDiscount::first()->price) {
            //             $siteShipmentPrice = 0;
            //         }


            //         return $item->getTotalPrice() + $siteShipmentPrice;
            //     }) ?? 0;
            // }

            // return 0;

            if ($this->user_id === auth()->id()) {
                try {
                    // Her ürünün Son Fiyat'larının toplamı (ürün ve kampanya indirimleri dahil)
                    $finalTotal = $this->items->sum(function ($item) {
                        return $item->getTotalPrice();
                    });

                    // Kargo ücreti kontrolü
                    $shipmentPrice = SiteSetting::first()->site_shipment_price ?? 0;
                    $shipmentDiscountPrice = ShipmentDiscount::first()->price ?? 0;

                    // Kupon indirimi
                    if ($this->coupon_id && $this->coupon && $this->coupon->isValid()) {
                        $couponDiscount = $this->coupon->discount_type === 'fixed'
                            ? min($this->coupon->discount_value, $finalTotal)
                            : $finalTotal * ($this->coupon->discount_value / 100);

                        $finalTotal = max(0, $finalTotal - $couponDiscount);
                    }

                    // En son kargo ücreti eklenir
                    if ($finalTotal < $shipmentDiscountPrice) {
                        $finalTotal += $shipmentPrice;
                    }

                    return round($finalTotal, 2);

                } catch (\Exception $e) {
                    Log::error('Cart total calculation error:', [
                        'error' => $e->getMessage(),
                        'cart_id' => $this->id
                    ]);
                    return 0;
                }
            }

        } catch (\Exception $e) {
            Log::error('Cart total calculation error: ' . $e->getMessage());
            return 0;
        }
    }


    public function getCartSummary(): array
    {
        $original_total = 0;
        $product_discounts = 0;
        $campaign_discounts = 0;
        $coupon_discount = 0; // Initialize coupon_discount

        foreach ($this->items as $item) {
            $original_price = $item->getOriginalPrice() * $item->quantity;
            $original_total += $original_price;

            $discounts = $item->getDiscountInfo();
            if (isset($discounts['product'])) {
                $product_discounts += ($original_price - ($discounts['product']['final_price'] * $item->quantity));
            }
            if (isset($discounts['campaign'])) {
                $campaign_discounts += ($original_price - ($discounts['campaign']['final_price'] * $item->quantity));
            }
        }

        $subtotal = $original_total - $product_discounts - $campaign_discounts;

        // Calculate shipping cost
        $shipping_cost = $this->calculateShippingCost($subtotal);

        // Apply coupon if exists
        // $coupon_discount = 0;
        if ($this->coupon && $this->coupon->isValid()) {
            $coupon_discount = $this->coupon->applyDiscount($subtotal);
        }

        $final_total = max(0, $subtotal - $coupon_discount + $shipping_cost);

        return [
            'original_total' => round($original_total, 2),
            'product_discounts' => round($product_discounts, 2),
            'campaign_discounts' => round($campaign_discounts, 2),
            'coupon_discount' => round($coupon_discount, 2),
            'shipping_cost' => $shipping_cost,
            'remaining_for_free_shipping' => max(0, config('cart.free_shipping_threshold', 250) - $subtotal),
            'final_total' => round($final_total, 2),
            'total_savings' => round($original_total - $final_total + $shipping_cost, 2),
        ];
    }

    public function calculateShippingCost(float $subtotal): float
    {
        $threshold = config('cart.free_shipping_threshold', 250);
        $default_cost = config('cart.shipping_cost', 30);

        return $subtotal >= $threshold ? 0 : $default_cost;
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }


}
