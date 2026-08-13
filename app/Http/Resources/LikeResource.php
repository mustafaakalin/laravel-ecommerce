<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => $this->product->price,
                'current_price' => $this->product->getCurrentPrice(),
                'old_price' => $this->product->old_price,
                'discount' => $this->product->discount,
                'rating' => $this->product->rating,
                'stock' => $this->product->stock,
                'is_active' => $this->product->is_active,
                'is_featured' => $this->product->is_featured,
                'is_new' => $this->product->is_new,
                'image' => $this->whenLoaded('product.images', function() {
                    return $this->product->images->first() ? $this->product->images->first()->image_path : null;
                }),
                'category' => new CategoryResource($this->whenLoaded('product.category')),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
