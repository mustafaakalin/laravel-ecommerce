<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'product' => new ProductResource($this->whenLoaded('product')),
            'order' => new OrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
