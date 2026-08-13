<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->getCurrentPrice(),
                    ],
                    'quantity' => $item->quantity,
                    'total_price' => $item->getTotalPrice()
                ];
            }),
            'total_items' => $this->calculateTotalItems(),
            'total_price' => $this->calculateTotalPrice(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
