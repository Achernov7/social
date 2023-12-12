<?php

namespace App\Http\Resources\Like;

use App\Http\Resources\Friend\FriendResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'whoLiked'=> FriendResource::make($this->user),
            'created_at' => $this->created_at
        ];
    }
}