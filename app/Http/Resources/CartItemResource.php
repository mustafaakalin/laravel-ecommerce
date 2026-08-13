<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->getCurrentPrice(),
            ],
            'quantity' => $this->quantity,
            'total_price' => $this->getTotalPrice(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}