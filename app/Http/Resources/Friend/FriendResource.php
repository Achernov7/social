<?php

namespace App\Http\Resources\Friend;

use Illuminate\Http\Request;
use App\Http\Resources\Image\MiniImageResource;
use Illuminate\Http\Resources\Json\JsonResource;



class FriendResource extends JsonResource
{

    public function miniImage($mini)
    {
        if ($mini){
            return MiniImageResource::make($mini);
        } else {
            return ['mini_url'=>url('/storage/images/default_image/mini.jpg')];
        }
    }
    
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
            'mini_image' => $this->miniImage($this->images->where('is_main_image', true)->first()),
        ];
    }

}