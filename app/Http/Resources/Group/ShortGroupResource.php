<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Request;
use App\Http\Resources\Image\MiniImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected function miniImage()
    {
        $miniImage = $this->images->where('is_main_image', true)->first();
        if ($miniImage) {
            return new MiniImageResource($miniImage);
        } else {
            return ['mini_url' => url('/storage/images/default_image/mini.jpg')];
        }
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mini_image' => $this->miniImage(),
        ];
    }
}
