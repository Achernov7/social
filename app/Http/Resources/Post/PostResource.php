<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Image\MiniImageResource;
use App\Http\Resources\Image\PreviewImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'description' => $this->description,
            'image'=> [
                'preview_image'=>PreviewImageResource::make($this->images->where('is_main_image', true)->first()),
                'mini_image'=> MiniImageResource::make($this->images->where('is_main_image', true)->first())
            ],
        ];
    }
}
