<?php

namespace App\Http\Resources\Friend;

use Illuminate\Http\Request;
use App\Http\Resources\Image\MiniImageResource;
use App\Http\Resources\Image\MicroImageResource;
use Illuminate\Http\Resources\Json\JsonResource;



class FriendResourceWithMicroImage extends JsonResource
{

    public function miniImage($mini)
    {
        if ($mini){
            return MiniImageResource::make($mini);
        } else {
            return ['mini_url'=>url('/storage/images/default_image/mini.jpg')];
        }
    }

    public function microImage($mini)
    {
        if ($mini){
            return MicroImageResource::make($mini);
        } else {
            return ['micro_url'=>url('/storage/images/default_image/micro.jpg')];
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
            'micro_image' => $this->microImage($this->images->where('is_main_image', true)->first()),
        ];
    }

}