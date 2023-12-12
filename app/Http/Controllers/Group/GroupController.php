<?php

namespace App\Http\Controllers\Group;

use App\Components\SharedServices\getNameWithSurnameFromSearchingUser;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Group\GetRequest;
use App\Http\Requests\Group\StoreRequest;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Friend\FriendResource;
use App\Traits\tableImagesConnectedWithObject;
use App\Http\Requests\Group\GetRequestForSubscribersOrBanUsers;

class GroupController extends Controller
{
    protected $image = false;

    protected $links = false;

    protected $data;

    protected $groupsToAdd;

    protected $alreadyTook = [];

    use tableImagesConnectedWithObject;

    public function create(StoreRequest $request)
    {
        try{

            DB::beginTransaction();

            $this->data = $request->validated();
            
            $this->prepareData();

            $this->data['creator_id'] = Auth::id();
            $group = Group::create($this->data);

            if($this->image){
                $this->SaveMainImageInImageTable($this->image, $group->id, 'group');
            }

            DB::commit();

            return response()->json([
                'message' => 'group created successfully',
            ]);

        } catch (\exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception -> getMessage(),
            ], 422);
        }
    }

    public function index(GetRequest $request)
    {
        $this->data = $request->validated();

        if (isset($this->data['elementsAlreadyTook'])){

            $this->alreadyTook = $this->data['elementsAlreadyTook'];

            return $this->indexForGroupsThatNotYours();
        }

        $groups =  Group::where('creator_id', auth()->user()->id)->skip($this->data['page']*$this->data['limit'])->take($this->data['limit'])->latest();;

        if (($this->data['search'])){
            $groups = $groups->where('name', 'like', '%' . $this->data['search'] . '%')->get();
        } else {
            $groups = $groups->get();
        }
        
        $groups->map(function($group){
            $group->authenticated = 'creator';
            $group->setRelation('subsribersCount', $group->users()->where('ban', false)->where('user_id', '!=', auth()->user()->id)->count());
            $group->setRelation('bansCount', $group->users()->where('ban', true)->where('user_id', '!=', auth()->user()->id)->count());
        });


        
        if (count($groups) == $this->data['limit']){

            return GroupResource::collection($groups);

        } else {
            $this->data['limit'] = $this->data['limit'] - count($groups);
            $this->groupsToAdd = $groups;

            //Exclude groups where user are banned
            $groupsWhereUserBanned = Group::whereHas('users', function($q){
                $q->where('user_id', Auth::id())->where('ban', true);
            })->get()->pluck('id')->toArray();

            $this->alreadyTook = $groupsWhereUserBanned;

            return $this->indexForGroupsThatNotYours();
        }

    }

    protected function indexForGroupsThatNotYours()
    {
        if (!isset($this->data['secondGroupOfObjects'])){
            $groupsSubcribeTo = $this->indexForGroupsThatUserSubscribeTo();
        }

        if (isset($groupsSubcribeTo['needToAdditionalLoad']) && !$groupsSubcribeTo['needToAdditionalLoad']){
            return GroupResource::collection($groupsSubcribeTo['groups'])->additional([
                'alreadyTook' => $this->alreadyTook,
            ]);
        }

        $groups =  Group::where('creator_id', '!=' , auth()->user()->id)->take($this->data['limit'])->latest();
        
        if (!$this->alreadyTook){
            $groups =  $groups->skip($this->data['page']*$this->data['limit']);
        } else {
            $groups =  $groups->whereNotIn('id', $this->alreadyTook);
        }
        
        if (($this->data['search'])){
            $groups = $groups->where('name', 'like', '%' . $this->data['search'] . '%')->get();
        } else {
            $groups = $groups->get();
        }


        $groups->map(function($group){
            $group->authenticated = 'notConnectedwithYou';
            $group->setRelation('subsribersCount', $group->users()->where('ban', false)->where('user_id', '!=', auth()->user()->id)->count());
            return $group;
        });
        
        array_push($this->alreadyTook, ...$groups->pluck('id')->toArray());


        if ($this->groupsToAdd){
            $groups = $this->groupsToAdd->concat($groups);
        }

        return GroupResource::collection($groups)->additional([
            'alreadyTook' => $this->alreadyTook,
        ]);
    }

    public function indexForGroupsThatUserSubscribeTo()
    {
        $groups = Auth::user()->groups()->where('groups.creator_id', '!=', auth()->user()->id)->whereNotIn('groups.id', $this->alreadyTook)->take($this->data['limit']);

        if (($this->data['search'])){
            $groups = $groups->where('name', 'like', '%' . $this->data['search'] . '%')->get();
        } else {
            $groups = $groups->get();
        }

        if (count($groups) != 0){
            array_push($this->alreadyTook, ...$groups->pluck('id')->toArray());

            $groups->map(function($group){
                $group->authenticated = 'subscribeTo';
                $group->setRelation('subsribersCount', $group->users()->where('ban', false)->where('user_id', '!=', auth()->user()->id)->count());
                return $group;
            });
        }

        if (count($groups) == $this->data['limit']){
            if ($this->groupsToAdd){ 
                $groups = $this->groupsToAdd->concat($groups);
            }
            return ['needToAdditionalLoad' => false, 'groups'=>$groups];

        } else {
            if (count($groups) != 0){
                
                if ($this->groupsToAdd){ 
                    $this->groupsToAdd = $this->groupsToAdd->concat($groups);
                } else {
                    $this->groupsToAdd = $groups;
                }
                $this->data['limit'] = $this->data['limit'] - count($groups);
            }
            return ['needToAdditionalLoad' => true];
        }
        
    }


    public function update(Group $group, StoreRequest $request)
    {

        $this->authorize('admin', $group);
        
        try{
            DB::beginTransaction();

            $this->data = $request->validated();

            $this->prepareData();

            if($this->image){ 
                $group->images()->where('is_main_image', true)->update(['is_main_image' => false]);
                $this->SaveMainImageInImageTable($this->image, $group->id, 'group');
            }
    
            $group->update($this->data);
    
            DB::commit();
            return response()->json([
                'message' => 'group updated successfully',
            ]);

        } catch (\exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception -> getMessage(),
            ], 422);
        }
            
    }

    public function getSubscribers(Group $group, GetRequestForSubscribersOrBanUsers $request)
    {
        return $this->getSubscribersOrBanUsers($group, $request, false);
    }

    public function getBanUsers(Group $group, GetRequestForSubscribersOrBanUsers $request)
    {
        $this->authorize('admin', $group);

        return $this->getSubscribersOrBanUsers($group, $request, true);  
    }

    public function subscribe(Group $group)
    {
        $this->authorize('notSubscribed', $group);

        $group->users()->attach(Auth::id());

        return response()->json([
            'message' => 'successfully subscribed',
        ]);
    }

    public function unsubscribe(Group $group)
    {
        $this->authorize('notBanned', $group);

        $this->authorize('notAdmin', $group);

        $group->users()->detach(Auth::id());

        return response()->json([
            'message' => 'successfully unsubscribed',
        ]);
    }

    private function getSubscribersOrBanUsers($group, $request, $valueOfBan)
    {
        $data = $request->validated();

        $users = Group::where('id', $group->id);

        if (!isset($data['IdsOfAlreadyExistedUsers'])){

            if (!isset($data['searchingUser'])){
                
                $users = $users->with(['users'=>function($q) use ($data, $valueOfBan){
                    $q->where('user_id', '!=', Auth::id())->where('ban', $valueOfBan)->take($data['limit']);
                }]);
            } else {
                
                if (str_contains($data['searchingUser'], ' ') || str_contains($data['searchingUser'], '&nbsp;')){

                    $nameOrSuraname = getNameWithSurnameFromSearchingUser::getNameSurname($data['searchingUser']);
                    
                    if (gettype($nameOrSuraname) == 'string'){
                        
                        $users = $users->with(['users'=>function($q) use ($data, $valueOfBan, $nameOrSuraname){
                            $q->where('user_id', '!=', Auth::id())->where('ban', $valueOfBan)->where(function($q) use ($nameOrSuraname){
                                $q->where('name', 'like', '%' . $nameOrSuraname . '%')->orWhere('surname', 'like', '%' . $nameOrSuraname . '%');
                            })->take($data['limit']);
                        }]);

                    } else if (gettype($nameOrSuraname) == 'array'){
                        
                        $users = $users->with(['users'=>function($q) use ($data, $valueOfBan, $nameOrSuraname){
                            $q->where('user_id', '!=', Auth::id())->where('ban', $valueOfBan)->where(function($q) use ($data, $nameOrSuraname){
                                $q->where(function($q) use ( $nameOrSuraname){
                                    $q->where('name', 'like', '%' . $nameOrSuraname[0] . '%')->where('surname', 'like', '%' . $nameOrSuraname[1] . '%');
                                })->orWhere(function($q) use ( $nameOrSuraname){
                                    $q->where('name', 'like', '%' . $nameOrSuraname[1] . '%')->where('surname', 'like', '%' . $nameOrSuraname[0] . '%');
                                });
                            })->take($data['limit']);
                        }]);
                    }
                    
                } else {

                    $users = $users->with(['users'=>function($q) use ($data, $valueOfBan){
                        $q->where('user_id', '!=', Auth::id())->where('ban', $valueOfBan)->where(function($q) use ($data){
                            $q->where('name', 'like', '%' . $data['searchingUser'] . '%')->orWhere('surname', 'like', '%' . $data['searchingUser'] . '%');
                        })->take($data['limit']);
                    }]);
                    
                }

            }

        } else {

            array_push($data['IdsOfAlreadyExistedUsers'], Auth::id());

            if (!isset($data['searchingUser'])){
                $users = $users->with(['users'=>function($q) use ($data, $valueOfBan){
                    $q->whereNotIn('user_id', $data['IdsOfAlreadyExistedUsers'])->where('ban', $valueOfBan)->take($data['limit']);
                }]);
            } else {

                if (str_contains($data['searchingUser'], ' ') || str_contains($data['searchingUser'], '&nbsp;')){

                    $nameOrSuraname = getNameWithSurnameFromSearchingUser::getNameSurname($data['searchingUser']);

                    if (gettype($nameOrSuraname) == 'string'){
                        $users = $users->with(['users'=>function($q) use ($data, $valueOfBan, $nameOrSuraname){
                            $q->whereNotIn('user_id', $data['IdsOfAlreadyExistedUsers'])->where('ban', $valueOfBan)->where(function($q) use ($nameOrSuraname){
                                $q->where('name', 'like', '%' . $nameOrSuraname . '%')->orWhere('surname', 'like', '%' . $nameOrSuraname . '%');
                            })->take($data['limit']);
                        }]);
                    } else if (gettype($nameOrSuraname) == 'array'){

                        $users = $users->with(['users'=>function($q) use ($data, $valueOfBan, $nameOrSuraname){
                            $q->whereNotIn('user_id', $data['IdsOfAlreadyExistedUsers'])->where('ban', $valueOfBan)->where(function($q) use ( $nameOrSuraname){
                                $q->where(function($q) use ($nameOrSuraname){
                                    $q->where('name', 'like', '%' . $nameOrSuraname[0] . '%')->where('surname', 'like', '%' . $nameOrSuraname[1] . '%');
                                })
                                ->orWhere(function($q) use ($nameOrSuraname){
                                    $q->where('name', 'like', '%' . $nameOrSuraname[1] . '%')->where('surname', 'like', '%' . $nameOrSuraname[0] . '%');
                                });
                            })->take($data['limit']);
                        }]);

                    }

                } else {                    
                    $users = $users->with(['users'=>function($q) use ($data, $valueOfBan){
                        $q->whereNotIn('user_id', $data['IdsOfAlreadyExistedUsers'])->where('ban', $valueOfBan)->where(function($q) use ($data){
                            $q->where('name', 'like', '%' . $data['searchingUser'] . '%')->orWhere('surname', 'like', '%' . $data['searchingUser'] . '%');
                        })->take($data['limit']);
                    }]);
                }
            }
        }

        $users = $users->get()->first()->users;

        return FriendResource::collection($users);
    }

    public function banUser(Group $group, User $user)
    {
        $this->commonForUnbanAndBanUsers($group, $user, true);

        return response()->json([
            'message' => 'user banned successfully',
        ]);
    }

    public function unbanUser(Group $group, User $user)
    {
        $this->commonForUnbanAndBanUsers($group, $user, false);

        return response()->json([
            'message' => 'user unbanned successfully',
        ]);
    }

    private function commonForUnbanAndBanUsers($group, $user, $updateBanTo)
    {
        if ($group->creator_id == $user->id){
            return response()->json([
                'message' => 'you can\'t ban or unban yourself',
            ], 422);
        } else {
            $this->authorize('admin', $group);

            $group->users()->updateExistingPivot($user->id, ['ban' => $updateBanTo]);

        }
    }

    public function destroy(Group $group)
    {
        $this->authorize('admin', $group);
        
        $this->deleteImagesFromTable($group);

        $group->delete();

        return response()->json([
            'message' => 'group deleted successfully',
        ]);

    }

    private function prepareData():void
    {
        if(isset($this->data['image'])){
            $this->image = $this->data['image'];
            unset($this->data['image']);
        }

        if (isset($this->data['links'])) {
            $this->data['links'] = implode(',', $this->data['links']);
        }
    }

}