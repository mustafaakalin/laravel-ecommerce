<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'identity_number' => $this->identity_number,
            'avatar' => $this->avatar,
            'instagram_account' => $this->instagram_account,
            'facebook_account' => $this->facebook_account,
            'tiktok_account' => $this->tiktok_account,
            'x_account' => $this->x_account,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'addresses' => AddressResource::collection($this->addresses),
            'orders' => OrderResource::collection($this->orders),
        ];
    }
}
