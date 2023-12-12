<?php

namespace App\Policies\Group;

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class GroupPolicy
{

    public function admin(User $user, Group $group)
    {
        return $user->id === $group->creator_id;
    }

    public function notAdmin(User $user, Group $group)
    {
        return $user->id !== $group->creator_id;
    }

    public function notBanned(User $user, Group $group)
    {
        $group = DB::table('group_user')->where('user_id', $user->id)->where('group_id', $group->id)->get();

        if ($group->count() == 0) {
            return true;
        } else {
            if ($group[0]->ban == true) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function notSubscribed(User $user, Group $group){
        $group = DB::table('group_user')->where('user_id', $user->id)->where('group_id', $group->id)->get();

        if ($group->count() == 0) {
            return true;
        } else {
            return false;
        }
    }

}
