<?php

namespace App\Policies\Image;

use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImagePolicy
{

    public function usersImage(User $user, Image $image)
    {

        if ($user->images()->where('id', $image->id)->exists()) {
            return true;
        }
        return false;
    }

}
