<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MostSoldCategoriesComponentForMobileResource extends JsonResource
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
            'icon' => $this->icon,
            'total_sales' => $this->total_sales,
            'products_count' => $this->active_products_count ?? 0,
            // 'image' => $this->getFirstMediaUrl('image'),
            'rank' => $this->rank,
        ];
    }
}
