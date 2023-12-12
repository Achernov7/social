<?php

namespace App\Http\Controllers\Image;

use App\Models\Image;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Image\GetRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Image\StoreRequest;
use App\Http\Requests\Image\UpdateRequest;
use App\Traits\tableImagesConnectedWithObject;
use App\Http\Resources\Image\ImageResourceFull;

class ImageController extends Controller
{
    use tableImagesConnectedWithObject;
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        
        $images = collect([]);

        foreach ($data['images'] as $image) {
            $imageToSave = $this->SaveImageInImageTable($image, Auth::user()->id, 'user');
            $images->push($imageToSave);
        }

        return ImageResourceFull::collection($images);

    }

    public function index(GetRequest $request)
    {

        $data = $request->validated();

        $images = Auth::user()->images()->orderBy('id', 'desc')->limit($data['limit']);
        
        if (isset($data['idsAlreadyGetted'])) {
            $images = $images->whereNotIn('id', $data['idsAlreadyGetted']);
        }
        $images = $images->get();

        return ImageResourceFull::collection($images);
    }

    public function setMain($id)
    {
        try{
            DB::beginTransaction();

            Auth::user()->images()->where('is_main_image', true)->limit(1)->update(['is_main_image' => false]);

            Auth::user()->images()->find($id)->update(['is_main_image' => true]);

            DB::commit();
            
            return response()->json([
                'message' => 'main image updated successfully',
            ]);
            
        } catch (\exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception -> getMessage(),
            ], 422);
        }

    }

    public function updateDescription(Image $image, UpdateRequest $request)
    {

        $this->authorize('usersImage', $image);

        $data = $request->validated();

        $image->update([
            'description' => $data['description']
        ]);

        return response()->json([
            'message' => 'description updated successfully',
        ]);
    }

    public function unsetMain($id)
    {
        Auth::user()->images()->find($id)->update(['is_main_image' => false]);

        return response()->json([
            'message' => 'main image updated successfully',
        ]);
    }

    public function destroy(Image $image)
    {
        $this->authorize('usersImage', $image);

        Auth::user()->images()->find($image->id)->delete();
        
        if (!str_contains($image->preview_url, 'examples/')){
            $this->deleteImageFromDisc($image);
        }

        return response()->json([
            'message' => 'image deleted successfully',
        ]);

    }
}