<?php
namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;


class UserResourceCollection extends ResourceCollection
{

    public function WithIsAuthUser()
    {

        $this->collection->each(function ($item) {
            if (Auth::user()->id == $item->id) {
                $item->AuthUser = true;
            }
        });

        return $this;
    }

}
