<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->items),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
