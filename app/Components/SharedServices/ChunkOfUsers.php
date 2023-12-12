<?php

namespace App\Components\SharedServices;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChunkOfUsers
{
    protected $model;
    public $chunkSize;
    public $from;
    public $accepted;
    public $additionalParams;
    protected $totalNumberOfRequestFriends;
    protected $sharedFriends;
    protected $alreadyDisplayed;

    public function __construct($model, $chunkSize = 10, $from = 0,  $accepted = 1, $additionalParams = [], $alreadyDisplayed = false)
    {
        $this->model = $model;

        $this->chunkSize = $chunkSize;
        $this->from = $from;
        $this->accepted =  $accepted;
        $this->additionalParams = $additionalParams;
        $this->alreadyDisplayed = $alreadyDisplayed;

    }

    public function chunksOfAllFriendsOrNot()
    {
        if ($this->alreadyDisplayed) {
            return $this->chunksWithAlreadyDisplayedForRequestedFriends();
        }

        $this->totalNumberOfRequestFriends = $this->model->RequestToBeFriend()->where('accepted', $this->accepted)->count();

        if ($this->from >= $this->totalNumberOfRequestFriends) {

            $this->from = $this->from - $this->totalNumberOfRequestFriends;

            return $this->getSubscribeFriends();
        }

        $friends = $this->model->RequestToBeFriend()->getFriendsWithPaginate($this->chunkSize, $this->from, $this->accepted);

        $this->additionalParams ? $friends = $friends->SearchWithAdditionalParams($this->additionalParams)->get() : $friends = $friends->get();

        if ($friends->count() < $this->chunkSize) {

            $this->sharedFriends = $friends;
            $this->from = 0;
            $this->chunkSize = $this->chunkSize - $friends->count();
            return $this->getSubscribeFriends();

        } else {
            return $friends;
        }
    }

    protected function chunksWithAlreadyDisplayedForRequestedFriends()
    {
        $chunksOfRequestedFriends = $this->model->RequestToBeFriend()->whereNotIn('users.id', $this->alreadyDisplayed)->limit($this->chunkSize)->where('accepted', $this->accepted);

        $this->additionalParams ? $chunksOfRequestedFriends = $chunksOfRequestedFriends->SearchWithAdditionalParams($this->additionalParams)->get() : $chunksOfRequestedFriends = $chunksOfRequestedFriends->get();

        if ($chunksOfRequestedFriends->count() < $this->chunkSize) {
            $this->sharedFriends = $chunksOfRequestedFriends;
            $this->from = 0;
            $this->chunkSize = $this->chunkSize - $chunksOfRequestedFriends->count();
            return $this->chunksWithAlreadyDisplayedForSubscribedFriends();
        } else {
            return $chunksOfRequestedFriends;
        }
    }

    protected function chunksWithAlreadyDisplayedForSubscribedFriends()
    {
        $chunksOfSubscribeFriends = $this->model->SubscribeToUser()->whereNotIn('users.id', $this->alreadyDisplayed)->limit($this->chunkSize)->where('accepted', $this->accepted);

        $this->additionalParams ? $chunksOfSubscribeFriends = $chunksOfSubscribeFriends->SearchWithAdditionalParams($this->additionalParams)->get() : $chunksOfSubscribeFriends = $chunksOfSubscribeFriends->get();

        if ($this->sharedFriends){
            $chunksOfSubscribeFriends = $this->sharedFriends->concat($chunksOfSubscribeFriends);
        }
        return $chunksOfSubscribeFriends;
    }


    protected function getSubscribeFriends()
    {
        $subscribeFriends = $this->chunksOfSubscribedToOfUser();

        if ( $this->sharedFriends){
            $subscribeFriends = $this->sharedFriends->concat($subscribeFriends);
        }

        return $subscribeFriends;
    }

    public function chunksOfSubscribersOfUser()
    {
        if ($this->additionalParams){
            return $this->model->RequestToBeFriend()->SearchWithAdditionalParams($this->additionalParams)->getFriendsWithPaginate($this->chunkSize, $this->from, $this->accepted)->get()->shuffle();
        } else {
            return $this->model->RequestToBeFriend()->getFriendsWithPaginate($this->chunkSize, $this->from, $this->accepted)->get()->shuffle();
        }
    }

    public function chunksOfSubscribedToOfUser()
    {
        if ($this->additionalParams){
            return $this->model->SubscribeToUser()->SearchWithAdditionalParams($this->additionalParams)->getFriendsWithPaginate($this->chunkSize, $this->from, $this->accepted)->get()->shuffle();
        } else {
            return $this->model->SubscribeToUser()->getFriendsWithPaginate($this->chunkSize, $this->from, $this->accepted)->get()->shuffle();
        }
    }

    public function chunksOfUsersWithNoRelationShipOfUser()
    {
        if ($this->additionalParams){
            $AllUsersWithRelationShipWithThisUser = $this->model->SubscribeToUser()->SearchWithAdditionalParams($this->additionalParams)->get()->pluck('id')->merge($this->model->RequestToBeFriend()->SearchWithAdditionalParams($this->additionalParams)->get()->pluck('id')->merge(Auth::user()->id));
            $UsersWithNoRelationShipWithThisUser = User::SearchWithAdditionalParams($this->additionalParams)->whereNotIn('id', $AllUsersWithRelationShipWithThisUser)->skip($this->from)->take($this->chunkSize)->get();
        } else {
            $AllUsersWithRelationShipWithThisUser = $this->model->SubscribeToUser()->get()->pluck('id')->merge($this->model->RequestToBeFriend()->get()->pluck('id')->merge(Auth::user()->id));
            $UsersWithNoRelationShipWithThisUser = User::whereNotIn('id', $AllUsersWithRelationShipWithThisUser)->skip($this->from)->take($this->chunkSize)->get();
        }

        return $UsersWithNoRelationShipWithThisUser->shuffle();
    }


}

