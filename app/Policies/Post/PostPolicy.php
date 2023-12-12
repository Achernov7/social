<?php

namespace App\Policies\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PostPolicy
{

    public function notBanned(User $user, Post $post)
    {

        if ($post->postable_type == 'group') {
            $ban= DB::table('group_user')->where('user_id', $user->id)->where('group_id', $post->postable_id)->get();
            
            if ($ban->isEmpty()) {
                return true;
            } else {
                if ($ban[0]->ban) {
                    return false;
                } else {
                    return true;
                }
            }
        }        
    }

}
