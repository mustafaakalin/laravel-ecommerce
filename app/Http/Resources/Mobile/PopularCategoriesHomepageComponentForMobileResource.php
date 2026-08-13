<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopularCategoriesHomepageComponentForMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
    $totalProducts = ($this->direct_products_count ?? 0) + ($this->child_products_count ?? 0);
    
    return [
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'icon' => $this->icon,
        'total_sales' => $this->total_sales ?? 0,
        'total_comments' => $this->total_comments ?? 0,
        'average_rating' => round($this->average_rating ?? 0, 1),
        'products_count' => $totalProducts,
        'products_details' => [
            'direct_count' => $this->direct_products_count ?? 0,
            'children_count' => $this->child_products_count ?? 0
        ],
        'popularity_score' => $this->popularity_score ?? 0,
        'rank' => $this->rank,
    ];
    }
}
