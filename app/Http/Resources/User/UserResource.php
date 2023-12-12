<?php

namespace App\Http\Resources\User;



use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\Friend\FriendResource;
use App\Components\SharedServices\ChunkOfUsers;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Image\PreviewImageResource;
use App\Http\Resources\Friend\FriendResourceOnlyIds;

class UserResource extends JsonResource
{

    public $AuthUser;

    public $model;

    public $chunkSize;

    public $from;

    public $accepted;

    public function __construct($resource, $chunkSize = 10, $from = 0, $accepted = 1) {
        parent::__construct($resource);

        $this->chunkSize = $chunkSize;
        $this->from = $from;
        $this->accepted = $accepted;
    }

    public function WithIsAuthUser()
    {
        if (!(Auth::user()->id == $this->id)) {
            $this->AuthUser = true;
        }
        return $this;
    }


    protected function previewImage($preview)
    {
        if ($preview){
            return PreviewImageResource::make($preview);
        } else {
            return ['preview_url'=>url('/storage/images/default_image/default.jpg')];
        }
    }

    protected function last_activity()
    {
        if (Redis::get('usersLastOnline:'.$this->id)){
            if (Carbon::now()->lt( Carbon::parse(Redis::get('usersLastOnline:'.$this->id))->addMinutes(1)) ) {
                return 'Online';
            } else {
                return Carbon::parse(Redis::get('usersLastOnline:'.$this->id))->diffForHumans();
            }
        } else if ($this->last_activity != null) { 
            return Carbon::parse($this->last_activity)->diffForHumans();
        } else {
            return null;
        }
    }
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $ServiceForChunkOfUsers = new ChunkOfUsers($this, $this->chunkSize, $this->from, $this->accepted);
        $userPropertis = [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'birthdayDate' => $this->birthdayDate,
            'preview_image' => $this->previewImage($this->images->where('is_main_image', true)->first()),
            'town' => $this->town,
            'gender' => $this->gender,
            'familyStatus' => $this->familyStatus,
            'about' => $this->about,
        ];

        if ($this->AuthUser) {
            $userPropertis['friends'] = FriendResourceOnlyIds::collection(Auth::user()->realFriends());
            $userPropertis['subscribers'] = FriendResourceOnlyIds::collection(Auth::user()->requestToBeFriend()->wherePivot('accepted', 0)->get());
            $userPropertis['AuthUser'] = Auth::user()->id;


            $userPropertis['someoneElsesFriends'] = FriendResource::collection($ServiceForChunkOfUsers->chunksOfAllFriendsOrNot());
            $userPropertis['someoneElsesSubscribers'] = FriendResourceOnlyIds::collection($this->requestToBeFriend()->wherePivot('accepted', 0)->get());
            $userPropertis['last_activity'] = $this->last_activity();

            return $userPropertis;
        } else {
            $userPropertis['isAuthUser'] = 'isAuthUser';
            $userPropertis['friends'] = FriendResource::collection($ServiceForChunkOfUsers->chunksOfAllFriendsOrNot());
            return $userPropertis;
        }
    }

    public static function collection($resource){
        return new UserResourceCollection($resource);
    }

}
