<?php

namespace App\Http\Resources\Friend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\Image\MiniImageResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class FriendResourceWithRelationship extends JsonResource
{

    protected function miniImage($mini)
    {
        if ($mini){
            return MiniImageResource::make($mini);
        } else {
            return ['mini_url'=>url('/storage/images/default_image/mini.jpg')];
        }
    }

    protected function roleOfRelationship():string
    {
        if (Auth::user()->id == $this->id) {
            return 'You';
        } else {
            $relation = DB::table('friend_users')->where(function($q){
                $q->where('user_id', Auth::user()->id)->where('friend_id', $this->id);
            })->orWhere(
                function($q){
                    $q->where('user_id', $this->id)->where('friend_id', Auth::user()->id);
                }
            )->first();
            
            if ($relation == null){
                return 'WithNoRelationship';
            } else {
                if ($relation->accepted == 1){
                    return 'friends';
                }

                if ($relation->accepted == 0 && $relation->friend_id == Auth::user()->id){
                    return 'YoursSubscriber';
                }

                if ($relation->accepted == 0 && $relation->user_id == Auth::user()->id){
                    return 'YouSubscribeTo';
                }
            }
        }
    }

    protected function last_activity()
    {
        if (Redis::get('usersLastOnline:'.$this->id)){
            if (Carbon::now()->lt( Carbon::parse(Redis::get('usersLastOnline:'.$this->id))->addMinutes(1)) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'town' => $this->town,
            'mini_image' => $this->miniImage($this->images->where('is_main_image', true)->first()),
            'role' => $this->roleOfRelationship(),
            'online' => $this->last_activity(),
        ];
    }

}