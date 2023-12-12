<?php

namespace App\Http\Resources\ChatMessage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Image\PreviewImageResource;



class UsersInformationForChatResource extends JsonResource
{

    public function Image($image)
    {
        if ($image){
            return PreviewImageResource::make($image);
        } else {
            return ['preview_url'=>url('/storage/images/default_image/default.jpg')];
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
            'image' => $this->Image($this->images->where('is_main_image', true)->first()),
        ];
    }

}