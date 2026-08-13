<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'payment_method' => $this->payment_method,
            'card_name' => $this->card_name,
            'card_number' => $this->card_number,
            'expire_month' => $this->expire_month,
            'expire_year' => $this->expire_year,
            'cvc' => $this->cvc,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}