<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MostViewedProductsComponentForMobileResource extends JsonResource
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
            'sale_price' => $this->getCurrentPrice(),
            'view_count' => $this->view_count,
            'rating' => [
                'average' => $avgRating,
                'count' => $this->ratings->count(),
            ],
            'in_stock' => $this->isInStock(),
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'category' => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]
        ];
    }
}
