<?php 

namespace App\Components;

use Illuminate\Database\Eloquent\Builder;

class RegisterNewEloquentFunction
{
    public static function register()
    {
        Builder::macro('getFriendsWithPaginate', function ($howMany = 5, $from = 0, $accepted = 1) {
            return $this->where('friend_users.accepted', $accepted)->skip($from)->take($howMany);
        });

    }

}
