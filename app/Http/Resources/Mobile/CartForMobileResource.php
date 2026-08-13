<?php

namespace App\Http\Resources\Mobile;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Models\ShipmentDiscount;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class CartForMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        try {
            $items = $this->items->map(function($item) {
                $prices = $item->calculatePrices();
                
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'thumbnail' => $item->product->getFirstMediaUrl('thumbnail'),
                    ],
                    'quantity' => $prices['quantity'],
                    'pricing' => [
                        'original_price' => $prices['original_price'],
                        'product_discount' => $prices['product_discount'],
                        'campaign_discount' => $prices['campaign_discount'],
                        'final_unit_price' => $prices['final_unit_price'],
                        'total_price' => $prices['total_price'],
                        'total_savings' => $prices['total_savings'],
                    ]
                ];
            });

            // Calculate cart totals
            $subtotal = $items->sum('pricing.total_price');
            $originalTotal = $items->sum(fn($item) => $item['pricing']['original_price'] * $item['quantity']);
            $totalSavings = $items->sum('pricing.total_savings');

            // Apply coupon if exists
            $couponDiscount = 0;
            if ($this->coupon && $this->coupon->isValid()) {
                $couponDiscount = $this->coupon->type === 'fixed' 
                    ? min($this->coupon->value, $subtotal)
                    : $subtotal * ($this->coupon->value / 100);
                    
                $subtotal = max(0, $subtotal - $couponDiscount);
            }

            // Calculate shipping
            $shippingCost = 0;
            if ($subtotal < ShipmentDiscount::first()->price) {
                $shippingCost = SiteSetting::first()->site_shipment_price ?? 0;
            }

            $finalTotal = $subtotal + $shippingCost;

            return [
                'id' => $this->id,
                'items' => $items,
                'summary' => [
                    'original_total' => round($originalTotal, 2),
                    'subtotal' => round($subtotal, 2),
                    'total_item_savings' => round($totalSavings, 2),
                    'coupon' => $this->coupon ? [
                        'code' => $this->coupon->code,
                        'type' => $this->coupon->type,
                        'value' => $this->coupon->value,
                        'discount_amount' => round($couponDiscount, 2)
                    ] : null,
                    'shipping' => [
                        'cost' => round($shippingCost, 2),
                        'free_shipping_threshold' => ShipmentDiscount::first()->price,
                        'remaining_for_free' => max(0, ShipmentDiscount::first()->price - $subtotal),
                    ],
                    'final_total' => round($finalTotal, 2),
                    'total_savings' => round($totalSavings + $couponDiscount + 
                        ($subtotal >= ShipmentDiscount::first()->price ? $shippingCost : 0), 2)
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Cart resource error:', [
                'cart_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
