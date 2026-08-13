<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderForHomepageForMobileResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'link' => $this->link,
            'image_url' => $this->image, // asset('storage/' . $this->image)
        ];
    }
}
