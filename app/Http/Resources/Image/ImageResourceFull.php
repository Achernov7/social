<?php

namespace App\Http\Resources\Image;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResourceFull extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'url'=> url($this->url),
            'mini_url'=> url($this->mini_url),
            'preview_url'=> url($this->preview_url),
            'description'=> $this->description,
            'is_main_image'=> $this->is_main_image,
            'created_at'=> $this->created_at->diffForHumans(),
        ];
    }
}
