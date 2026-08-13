<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentForMobileResource extends JsonResource
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
            'status' => $this['status'] ?? null,
            'message' => $this['message'] ?? null,
            'client_secret' => $this['client_secret'] ?? null,
            'order_id' => $this['order_id'] ?? null,
        ];
    }
}
