<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

Trait tableImagesConnectedWithObject
{

    public function deleteImagesFromTable($object){

        $imagesToDelete = $object->images()->where('preview_url', 'not like', "/storage/images/examples/%")->get();

        if ($imagesToDelete !== null){
            foreach($imagesToDelete as $image){     
                self::deleteImageFromDisc($image);
            }

            $object->images()->where('preview_url', 'not like', "/storage/images/examples/%")->delete();
        }
    }

    public function deleteMainImageFromTable($object)
    {
        $imagesToDelete = $object->images()->first();
        if ($imagesToDelete === null) {
            return;
        }
        if (!str_contains($imagesToDelete->preview_url, '/storage/images/examples/')) {
            self::deleteImageFromDisc($imagesToDelete);
        }
        $object->images()->where('is_main_image', true)->delete();
    }

    static function deleteImageFromDisc($image)
    {
        $str = preg_split("/\//", $image->path);

        if (Storage::disk('public')->exists('images/'.$str[1])) {
            
            Storage::delete('public/images/'.$str[1]);
        }
        if (Storage::disk('public')->exists('images/mini_'.$str[1])) {
            
            Storage::delete('public/images/mini_'.$str[1]);
        }
        if (Storage::disk('public')->exists('images/micro_'.$str[1])) {
            
            Storage::delete('public/images/micro_'.$str[1]);
        }
        if (Storage::disk('public')->exists('images/prev_'.$str[1])) {
            
            Storage::delete('public/images/prev_'.$str[1]);
        }
    }

    public function SaveMainImageInImageTable($image, $id, $type){
        $this->SaveImageInImageTable($image, $id, $type, true);
    }

    public function SaveImageInImageTable($image, $id, $type, $isMainImage = false){
        $name= md5(Carbon::now().'_'.$image->getClientOriginalName()).'.'.$image->getClientOriginalExtension();
        $previewName = 'prev_'.$name;
        $miniName = 'mini_'.$name;
        $microName = 'micro_'.$name;
    
        $filePath = Storage::disk('public')->putFileAs('/images', $image, $name); 
        
        $imageToSave = Image::create([
            'path' => $filePath,
            'url'=> '/storage/'.$filePath,
            'mini_url' => '/storage/images/' . $miniName,
            'micro_url' => '/storage/images/' . $microName,
            'preview_url'=>'/storage/images/' . $previewName,
            'imageable_id' => $id,
            'imageable_type' => $type,
            'is_main_image' => $isMainImage
        ]);
        
        \Intervention\Image\Facades\Image::make($image)->fit(220, 220)
        ->save(storage_path('app/public/images/'.$previewName));
        
        \Intervention\Image\Facades\Image::make($image)->fit(60, 60)
        ->save(storage_path('app/public/images/'.$miniName));
        
        \Intervention\Image\Facades\Image::make($image)->fit(30, 30)
        ->save(storage_path('app/public/images/'.$microName));

        return $imageToSave;
    }
}