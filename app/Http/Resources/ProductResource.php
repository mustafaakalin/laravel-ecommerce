<?php

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $avgRating = $this->averageRating();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'sale_price' => $this->getCurrentPrice2(),
            'stock' => $this->stock > 0 ? $this->stock : 0,
            'in_stock' => $this->isInStock(),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_new' => $this->is_new,
            'discount' => $this->discount,
            // 'rating' => $this->rating,
            'rating' => [
                'average' => $avgRating,
                'count' => $this->ratings->count(),
            ],
            'campaign' => CampaignResource::collection($this->whenLoaded('campaigns')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'likes' => $this->likes->count(),
            // 'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'images' => $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumbnail' => $media->getUrl('thumbnail'),
                ];
            }),
            'specifications' => $this->specifications,
            'tags' => $this->tags,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'related_products' => ProductResource::collection($this->whenLoaded('relatedProducts')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
