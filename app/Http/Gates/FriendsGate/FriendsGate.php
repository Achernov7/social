<?php

namespace App\Http\Gates\FriendsGate;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendsGate 
{
    public function validRelation (User $user, $friend) {
        
        $relation = DB::table('friend_users')->where(function ($query) use ($user, $friend) { 
            $query->where('user_id', $user->id)->where('friend_id', $friend);})
        ->orWhere(function ($query) use ($user, $friend) { 
            $query->where('user_id', $user->id)->where('friend_id', $friend);})
        ->get();

        return $relation->count() == 0;
        
    }
}