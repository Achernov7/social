<?php

namespace App\Http\Controllers\Friend;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Friends\GetRequest;
use App\Http\Resources\Friend\FriendResource;
use App\Components\SharedServices\ChunkOfUsers;
use App\Http\Resources\Friend\FriendResourceWithRelationship;

class FriendController extends Controller
{
    public function Addfriend($user)
    {
        if (Gate::denies('valid-relation', $user)) {
            return response(['message' => 'The Relationship already exists'], 412);
        }

        Auth::user()->SubscribeToUser()->attach($user);

        return response()->json([
            'message' => 'You subscribed to this user',
        ]);
    }

    public function getFriendsForPaginationOfAnyUser(User $user, GetRequest $request)
    {
        $paginationData = $request->validated();
        $ServiceForChunkOfUsers = new ChunkOfUsers($user, $paginationData['limit'], $paginationData['page']*$paginationData['limit'], 1, [], $paginationData['alreadyTaken']);
        return FriendResource::collection($ServiceForChunkOfUsers->chunksOfAllFriendsOrNot())->resolve();
    }

    public function getFriendsOfAuthWithPagination(GetRequest $request)
    {
        return FriendResourceWithRelationship::collection($this->ReturnForServiceForChunkUsersWithAdditionalParams($request)->chunksOfAllFriendsOrNot())->resolve();
    }

    public function getSubscribersOfAuthWithPagination(GetRequest $request)
    {
        return FriendResourceWithRelationship::collection($this->ReturnForServiceForChunkUsersWithAdditionalParams($request, 0)->chunksOfSubscribersOfUser())->resolve();
    }


    public function getSubscribedToOfAuthWithPagination(GetRequest $request)
    {
        return FriendResourceWithRelationship::collection($this->ReturnForServiceForChunkUsersWithAdditionalParams($request, 0)->chunksOfSubscribedToOfUser())->resolve();
    }

    public function getUsersWithNoRelationshipWithAuthWithPagination(GetRequest $request)
    {
        return FriendResourceWithRelationship::collection($this->ReturnForServiceForChunkUsersWithAdditionalParams($request)->chunksOfUsersWithNoRelationShipOfUser())->resolve();
    }

    public function acceptToBeFriends($user, $alreadyDisplayed)
    {
        
        $totalTakenRequestFriends = DB::table('friend_users')->where('friend_id', $user)->where('accepted', 1)->count();
        
        Auth::user()->requestToBeFriend()->updateExistingPivot($user, [
            'accepted' => 1
        ]);
        if ($alreadyDisplayed <  $totalTakenRequestFriends){
            
            return response()->json([
                'message' => 'You are now friends with this user',
            ]);
        } else {
            return response()->json([
                'message' => 'You are now friends with this user',
                'AuthInfo' => FriendResource::make(Auth::user()),
            ]);
        }

    }

    public function deleteFromFriendList($user)
    {
        
        if (!Auth::user()->requestToBeFriend()->wherePivot('user_id', $user)->wherePivot('accepted', 1)->get()->isEmpty()) {
            Auth::user()->requestToBeFriend()->updateExistingPivot($user, [
                'accepted' => 0
            ]);
            return response()->json([
                'message' => 'The user is now your subscriber',
            ]);

        }
            
        if(!Auth::user()->SubscribeToUser()->wherePivot('friend_id', $user)->wherePivot('accepted', 1)->get()->isEmpty()) {
            Auth::user()->SubscribeToUser()->detach($user);

            return response()->json([
                'message' => 'You are no longer friends with this user',
            ]);
        }
    }

    public function CancelYourSubscription($user)
    {
        Auth::user()->SubscribeToUser()->wherePivot('friend_id', $user)->detach($user);

        return response()->json([
            'message' => 'You sucsessfully unsubscribed from this user',
        ]);
    }

    public function ReturnForServiceForChunkUsersWithAdditionalParams(GetRequest $request, $accepted = 1)
    {
        $paginationData = $request->validated();
        if (isset($paginationData['additionalParams'])){
            return new ChunkOfUsers(Auth::user(),  $paginationData['limit'], $paginationData['page']*$paginationData['limit'], $accepted, $paginationData['additionalParams']);
        } else {
            return new ChunkOfUsers(Auth::user(), $paginationData['limit'], $paginationData['page']*$paginationData['limit'], $accepted);
        }
    }

}