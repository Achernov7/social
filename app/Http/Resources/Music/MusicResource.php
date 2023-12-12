<?php

namespace App\Http\Resources\Music;

use App\Http\Resources\Image\MiniImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MusicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected function image($image)
    {
        if ($image){
            return MiniImageResource::make($image);
        } else {
            return ['mini_url'=>url('/storage/images/music/default/default.jpg')];
        }
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => url($this->url),
            'liked'=> $this->liked,
            'image'=> $this->image($this->image),
        ];
    }
}
