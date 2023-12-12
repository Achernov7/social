<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Image\MiniImageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Image\PreviewImageResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected $miniUrl;

    protected $previewUrl;

    protected function findImage($image){
        if ($image){
            $this->miniUrl = MiniImageResource::make($image);
            $this->previewUrl = PreviewImageResource::make($image);
        } else {
            $this->miniUrl = ['mini_url'=>url('/storage/images/default_image/mini.jpg')];
            $this->previewUrl = ['preview_url'=>url('/storage/images/default_image/preview.jpg')];
        }
    }

    public function toArray(Request $request): array
    {
        $this->findImage($this->images->where('is_main_image', true)->first());

        $arrayToReturn = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'links' => explode(',', $this->links),
            'mini_image' => $this->miniUrl,
            'preview_url' => $this->previewUrl,
            'authenticated' => $this->authenticated,
            'subscribers'=> $this->subsribersCount,
        ];

        if ($this->authenticated){
            $arrayToReturn['bansCount'] = $this->bansCount;
            return $arrayToReturn;
        } else {
            return $arrayToReturn;
        }
        
    }
}
