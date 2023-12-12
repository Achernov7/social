<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\User\UserResource;
use App\Traits\tableImagesConnectedWithObject;


class UserController extends Controller
{

    use tableImagesConnectedWithObject;

    public function store(StoreRequest $request)
    {
        try{
            DB::beginTransaction();

            $data = $request->validated();

            if(isset($data['image'])){
                $image = $data['image'];
                
                Auth::user()->images()->where('is_main_image', true)->limit(1)->update(['is_main_image' => false]);

                $this->SaveMainImageInImageTable($image, Auth::user()->id, 'user');
                
                unset($data['image']);
            }

            Auth::user()->update($data);

            DB::commit();
            
            return response()->json([
                'message' => 'user updated successfully',
            ]);
            
        } catch (\exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception -> getMessage(),
            ], 422);
        }

    }

    public function showAuthenticated()
    {
        $user = Auth::user();
        return UserResource::make($user)->resolve();
    }

    public function show(User $user)
    {
        return UserResource::make($user)->WithIsAuthUser()->resolve();
    }


    public function destroy(): void
    {
        Auth::guard('web')->logout();

        $this->deleteImagesFromTable(Auth::user());

        Auth::user()->delete();
    }

}