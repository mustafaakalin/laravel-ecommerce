<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialsHomepageComponentForMobileResource extends JsonResource
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
            'author' => $this->author,
            'position' => $this->position,
            'content' => $this->content,
            'rating' => $this->rating,
            'avatar' => $this->getFirstMediaUrl('avatar'),
            'created_at' => $this->created_at?->diffForHumans(),
        ];

    }
}
