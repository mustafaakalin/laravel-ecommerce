<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandsHomepageComponentForMobileResource extends JsonResource
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
            'logo' => $this->getFirstMediaUrl('logo'),
            'products_count' => $this->active_products_count ?? 0,
            'has_products' => ($this->active_products_count ?? 0) > 0,
            // 'sort_order' => $this->sort_order,
        ];
    }
}
