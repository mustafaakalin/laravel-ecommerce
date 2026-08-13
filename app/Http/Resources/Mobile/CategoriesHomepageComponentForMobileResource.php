<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesHomepageComponentForMobileResource extends JsonResource
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
            'icon' => $this->icon,
            'products_count' => $this->active_products_count ?? 0,
            'children_count' => $this->children_count ?? 0,
            'has_children' => $this->children_count > 0,
            'sort_order' => $this->sort_order,
            // 'image' => $this->getFirstMediaUrl('image'),
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
