<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopularProductsHomepageComponentForMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $avgRating = $this->averageRating();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'sale_price' => $this->getCurrentPrice2(),
            'stock' => $this->stock > 0 ? $this->stock : 0,
            'in_stock' => $this->isInStock(),
            'popularity_metrics' => [
                'cart_count' => $this->cart_items_count ?? 0,
                'sales_count' => $this->order_items_count ?? 0,
                'comments_count' => $this->comments_count ?? 0,
                'likes_count' => $this->likes_count ?? 0,
                'rating' => [
                    'average' => $avgRating,
                    'count' => $this->ratings_count ?? 0
                ]
            ],
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'category' => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'popularity_score' => $this->popularity_score,
            'rank' => $this->rank
        ];
    }
}
