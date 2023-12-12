<?php

namespace App\Traits;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Components\SharedServices\getTypeService;

Trait hasLikes
{    
    public function like(int $objectId)
    {
        $type = $this->getLikeAbleType();

        $userLikesThisObject = Like::where('user_id', Auth::id())->where('likeable_id', $objectId)->where('likeable_type', $type)->exists();
        
        if (!$userLikesThisObject){
            Like::create([
                'user_id' => Auth::id(),
                'likeable_id' => $objectId,
                'likeable_type' => $type,
            ]);
        }

        return response()->json([
            'message' => 'post liked successfully',
        ]);
    }


    public function dislike(int $objectId)
    {
        $type = $this->getDisLikeableType();

        $userLikesThisObject = Like::where('user_id', Auth::id())->where('likeable_id', $objectId)->where('likeable_type', $type)->exists();

        if ($userLikesThisObject){
            Like::where('user_id', Auth::id())->where('likeable_id', $objectId)->where('likeable_type', $type)->delete();
        }

        return response()->json([
            'message' => 'post disliked successfully',
        ]);
    }

    public function getLikesOfTheObject(int $objectId, $lastCreatedAt, int $numberOflikes)
    {
        $type = $this->getLikeableType();

        return Like::where('likeable_id', $objectId)->where('likeable_type', $type)->where('created_at', '<', $lastCreatedAt)->take($numberOflikes)->latest()->with('user')->get();
    }


    private function getLikeAbleType()
    {
        return $this->getType('like');
    }

    private function getDisLikeableType()
    {
        return $this->getType('dislike');
    }

    private function getType($type)
    {
        $pattern = "/.+\/(.+)(s)\/".preg_quote($type)."/";

        return getTypeService::getType($pattern);
    }
}