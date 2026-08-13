<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BestSellingProductsComponentForMobileResource extends JsonResource
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
            'total_sales' => $this->sales ?? 0,
            'stock' => $this->stock > 0 ? $this->stock : 0,
            'in_stock' => $this->isInStock(),
            'rating' => [
                'average' => $avgRating,
                'count' => $this->ratings->count(),
            ],
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'category' => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]
        ];
    }
}
