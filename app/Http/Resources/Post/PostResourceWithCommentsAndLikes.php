<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use App\Http\Resources\Like\LikeResource;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Group\ShortGroupResource;
use App\Http\Resources\Image\MiniImageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Image\PreviewImageResource;

class PostResourceWithCommentsAndLikes extends JsonResource
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
            'group' => ShortGroupResource::make($this->group),
            'likes_count' => $this->likes_count,
            'users_who_liked' => LikeResource::collection($this->likes),
            'comments' => CommentResource::collection($this->comments),
        ];
    }

}
