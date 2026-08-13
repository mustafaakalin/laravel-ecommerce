<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsForHomepageForProductMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'sale_price' => $this->getCurrentPrice2(),
            'thumbnail' => $this->getFirstMediaUrl('thumbnail'),
            'category' => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'stock' => $this->stock > 0 ? $this->stock : 0,
            'in_stock' => $this->isInStock(), // This will return boolean
            'rating' => $this->averageRating(),



                
            // Product::query()
            // ->select([
            //     'id', 
            //     'name', 
            //     'slug', 
            //     'price', 
            //     'stock',
            //     'category_id',
            //     'view_count',
            //     'is_active'
            // ])
            // ->with(['category', 'images', 'ratings'])
            // ->where('is_active', true)
            // ->where('stock', '>', 0)
            // ->orderBy('view_count', 'desc')
            // ->featured()
            // ->inStock()
            // ->latest()
            // ->take(4)
            // ->get()

        ];
    }
}
