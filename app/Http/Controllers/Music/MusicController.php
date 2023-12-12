<?php

namespace App\Http\Controllers\Music;

use Carbon\Carbon;
use App\Models\Image;
use App\Models\Music;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Music\GetRequest;
use App\Http\Requests\Music\GetRequestNextPrevious;
use App\Http\Requests\Music\GetRequstRandom;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Music\StoreRequest;
use App\Http\Resources\Music\MusicResource;

class MusicController extends Controller
{

    private $data;

    private $likedUsersIsOver = false;

    private $music;

    public function indexForLiked(GetRequest $request)
    {

        $this->data = $request->validated();

        if (isset($this->data['likedUsersIsOver'])){
            return $this->indexForNotLiked();
        }

        $this->index();
        
        $this->music->whereHas('users', function ($query) {
            $query->where('user_id', Auth::user()->id);
        });
        
        $this->music = $this->music->get();
        
        $this->music->map(function($music){
            $music->liked = true;
            return $music;
        });
        

        if (count($this->music) == $this->data['limit']){
            return MusicResource::collection($this->music);
        } else {
            $this->data['limit'] = $this->data['limit'] - count($this->music);

            $this->data['takenSongs'] = $this->music;

            $this->likedUsersIsOver = true;

            return $this->indexForNotLiked();
        }

    }

    protected function indexForNotLiked()
    {
        $this->index();
        
        $this->music->whereDoesntHave('users', function ($query) {
            $query->where('user_id',  Auth::user()->id);
        });

        $this->music = $this->music->get();
        
        $this->music->map(function($music){
            $music->liked = false;
            return $music;
        });

        if (isset($this->data['takenSongs'])){
            $this->music = $this->data['takenSongs']->concat($this->music);
        }
        
        if ( $this->likedUsersIsOver){
            return MusicResource::collection($this->music)->additional([
                'likedUsersIsOver' => true
            ]);
        } else {
            return MusicResource::collection($this->music);
        }
    }

    protected function index()
    {
        $this->music = Music::with('image')->orderBy('id', 'desc')->limit($this->data['limit']);

        if (isset($this->data['IdsOfAlreadyExistedSongs'])){
            $this->music = $this->music->whereNotIn('id', $this->data['IdsOfAlreadyExistedSongs']);
        }

        if (isset($this->data['searchingMusic'])){
            $this->music = $this->music->where('name', 'like', '%' . $this->data['searchingMusic'] . '%');
        }

        
    }


    public function song()
    {
        $music = Auth::user()->music()->orderBy('id', 'desc')->first();
        if ($music) {
            return MusicResource::make($music);

        } else {
            return response()->json([
                'message' => 'No music uploaded yet',
            ]);
        }
    }

    public function like($songId)
    {
        Auth::user()->music()->attach($songId);
        
        return response()->json([
            'message' => 'Liked successfully',
        ]);
    }

    public function unlike($songId)
    {
        
        Auth::user()->music()->detach($songId);

        $music = Music::find($songId);

        preg_match('/music\/'.Auth::user()->id.'\/(.+).mp3/', $music->path, $str);

        if ($music->users()->get()->count() == 0){
            
            preg_match('/music\/'.Auth::user()->id.'\/(.+).mp3/', $music->path, $str);
            if (Storage::disk('public')->exists('images/music/'.$str[1].'.jpg')) {

                Storage::delete('public/images/music/'.$str[1].'.jpg');
                $music->image()->delete();

            }
            if ( Storage::disk('public')->exists($music->path) ) {

                Storage::delete('public/'.$music->path);
                $music->delete();

            }

            return response()->json([
                'message' => 'Unliked successfully and music was deleted',
            ]);

        }

        return response()->json([
            'message' => 'Unliked successfully',
        ]);
        
    }

    public function next($songId, GetRequestNextPrevious $request)
    {
        $this->data = $request->validated();

        $signForSearching = '<';

        $orderForSearching = 'desc';

        return $this->checkIfYouLikedTheSong($signForSearching, $orderForSearching, $songId);

    }

    public function previous($songId, GetRequestNextPrevious $request)
    {

        $this->data = $request->validated();

        $signForSearching = '>';

        $orderForSearching = 'asc';

        return $this->checkIfYouLikedTheSong($signForSearching, $orderForSearching, $songId);
    }

    protected function checkIfYouLikedTheSong($signForSearching, $orderForSearching, $songId)
    {
        
        $music = Auth::user()->music()->wherePivot('music_id', $songId)->first();
        
        if ($music != null) {

            if (!$this->checkIfTheMusicIsFromTheSameRow($music)){
                $songId = null;
            }
            
            $this->music = Auth::user()->music();
            if ($songId != null){
                $this->music = $this->music->wherePivot('music_id', $signForSearching, $songId)->orderBy('id', $orderForSearching);
            } else {
                $this->music = $this->music->orderBy('id', 'desc');
            }

            return $this->responseForNextAndPrevious($songId, $orderForSearching, 'yours');
        } else {

            if (!$this->checkIfTheMusicIsFromTheSameRow(Music::find($songId))){
                $songId = null;
            }

            $this->music = Music::whereDoesntHave('users', function($query){
                $query->where('user_id', Auth::id());
            });

            if ($songId != null){
                $this->music = $this->music->where('id', $signForSearching, $songId)->orderBy('id', $orderForSearching);
            } else {
                $this->music = $this->music->orderBy('id', 'desc');
            }
            
            return $this->responseForNextAndPrevious($songId, $orderForSearching, 'notYours', $signForSearching);
        }
    }

    protected function responseForNextAndPrevious($songId, $orderForSearching, $whoseSong, $signForSearching = '')
    {

        $this->tryToAddSearchAndGetMusic();

        if ($this->music != null) {

            return MusicResource::make($this->music);

        } else {

            if ($whoseSong == 'yours') {

                $this->music = Auth::user()->music();

                if ($songId != null){
                    $this->music = $this->music->orderBy('id', $orderForSearching);
                } else {
                    $this->music = $this->music->orderBy('id', 'desc');
                }

                $this->tryToAddSearchAndGetMusic();

                if ($this->music != null) {
                    
                    return MusicResource::make($this->music);

                } else {

                    if ($this->music != null && $this->music->id != $songId) {
        
                        return MusicResource::make($this->music);
        
                    } else if ($this->music->id == $songId) {
        
                        return response()->json([
                            'message' => 'You have 1 song in your library',
                        ]);

                    } else {
        
                        return response()->json([
                            'message' => 'No music uploaded yet',
                        ]);

                    }
                }

            } else {

                $this->music = Auth::user()->music();

                if ($songId != null){
                    $this->music = $this->music->orderBy('id', $orderForSearching);
                } else {
                    $this->music = $this->music->orderBy('id', 'desc');
                }

                $this->tryToAddSearchAndGetMusic();

                if ($this->music != null) {
                    return MusicResource::make($this->music);
                } else {
                    $this->changeSings($signForSearching);
                    
                    $this->music = Music::whereDoesntHave('users', function($query){
                        $query->where('user_id',  Auth::id());
                    });

                    if ($songId != null){
                        $this->music = $this->music->where('id', $signForSearching, $songId)->orderBy('id', $orderForSearching);
                    } else {
                        $this->music = $this->music->orderBy('id', 'desc');
                    }

                    $this->tryToAddSearchAndGetMusic();

                    if ($this->music != null) {
                        return MusicResource::make($this->music);
                    } else {
                        return response()->json([
                            'message' => 'No music uploaded yet',
                        ]);
                    }
                }

            }
        } 
    }

    protected function checkIfTheMusicIsFromTheSameRow($music)
    {
        if (isset($this->data['search'])) {

            if (str_contains(strtolower($music->name), strtolower($this->data['search'])) ) {
                return true;
            } else {
                return false;
            }

        } else {
            return true;
        }
    }

    protected function changeSings(&$signForSearching)
    {
        if ($signForSearching == '<') {
            $signForSearching = '>';
        } else {
            $signForSearching = '<';
        }
    }

    protected function tryToAddSearchAndGetMusic()
    {
        if (isset($this->data['search'])) {
            $this->music = $this->music->where('name', 'like', '%' . $this->data['search'] . '%')->first();
        } else {
            $this->music = $this->music->first();            
        }
    }

    public function random($songId, GetRequstRandom $request)
    {
        $this->data = $request->validated();

        if (isset($this->data['search'])) {
            $music = Music::where('name', 'like', '%' . $this->data['search'] . '%')->inRandomOrder();

            if (isset($this->data['idsAlreadyPlayedInRandom'])) {
                $this->music = $music->whereNotIn('id', $this->data['idsAlreadyPlayedInRandom'])->first();
            } else {
                $this->music = $music->first();
            }

            if ($this->music != null) {
                return MusicResource::make($this->music);
            } else {
                $this->music = $music->first();

                if ($this->music != null){
                    return MusicResource::make($this->music);
                } else {
                    return $this->randomFromAll();
                }
            }
        } else {

            $music = Auth::user()->music()->wherePivot('music_id', $songId)->first();
    
            if ($music != null){
    
                $this->music = Auth::user()->music()->inRandomOrder()->wherePivot('music_id', '!=', $songId);
                if (isset($this->data['idsAlreadyPlayedInRandom'])) {
                    $this->music = $this->music->wherePivotNotIn('music_id', $this->data['idsAlreadyPlayedInRandom'])->first();
                } else {
                    $this->music = $this->music->first();
                }
    
                if ($this->music != null){
                    return MusicResource::make($this->music);
                } else {
                    return $this->randomFromAll($songId);
                }
    
            } else {
                return $this->randomFromAll($songId);
            }

        }

    }

    protected function randomFromAll()
    {
        if (isset($this->data['idsAlreadyPlayedInRandom'])) {
            $this->music = Music::inRandomOrder()->whereNotIn('id', $this->data['idsAlreadyPlayedInRandom'])->first();
        } else {
            $this->music = Music::inRandomOrder()->first();
        }

        if ($this->music != null){
            return MusicResource::make($this->music);
        } else {

            if (isset($this->data['idsAlreadyPlayedInRandom'])) {
                $this->music = Music::inRandomOrder()->first();

                if ($this->music != null){
                    return MusicResource::make($this->music);
                } else {
                    return response()->json([
                        'message' => 'No music uploaded yet',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'No music uploaded yet',
                ]);
            }
        }

    }


    public function store(StoreRequest $request)
    {
        $music = $request->validated()['music'];

        $uniqueNameWithoutExtension= md5(Carbon::now().'_'.$music->getClientOriginalName());
        $uniqueName= $uniqueNameWithoutExtension.'.'.$music->getClientOriginalExtension();

        $name = pathinfo($music->getClientOriginalName(), PATHINFO_FILENAME);

        $filePath = Storage::disk('public')->putFileAs('/music/'.Auth::user()->id, $music, $uniqueName);
        
        $music = Music::create([
            'path' => $filePath,
            'name' => $name,
            'url'=> '/storage/'.$filePath,
        ]);
        
        $music->users()->attach(Auth::user()->id);

        $musicPath = Storage::disk('public')->path($music->path);
        $imagePath = Storage::disk('public')->path('/images/music/'.$uniqueNameWithoutExtension.'.jpg');

        exec('ffmpeg -i '.$musicPath.' -filter:v scale=-2:60 -an '.$imagePath, $output, $status);

        if ($status == 0) {
            $image = Image::create([
                'path' => '/images/music/'.$uniqueNameWithoutExtension.'.jpg',
                'mini_url' => '/storage/images/music/' . $uniqueNameWithoutExtension.'.jpg',
                'imageable_id' => $music->id,
                'imageable_type' => 'music',
                'is_main_image' => true
            ]);

            $music->setRelation('image', $image);
        }
        
        return MusicResource::make($music);

    }

}