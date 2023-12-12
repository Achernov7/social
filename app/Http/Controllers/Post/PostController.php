<?php

namespace App\Http\Controllers\Post;
use Carbon\Carbon;

use App\Models\Post;
use App\Models\Group;
use App\Traits\hasLikes;
use App\Traits\hasComments;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Post\GetRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\Like\LikeResource;
use App\Http\Resources\Post\PostResource;
use App\Http\Requests\Post\GetRequestForLikes;
use App\Traits\tableImagesConnectedWithObject;
use App\Http\Resources\Comment\CommentResource;
use App\Components\SharedServices\getTypeService;
use App\Http\Requests\Post\GetRequestForComments;
use App\Http\Requests\Post\GetRequestWithComments;
use App\Http\Resources\Post\PostResourceWithCommentsAndLikes;
use App\Http\Requests\Comment\StoreRequest as StoreRequestComment;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public $type;

    public $model;

    public $image = false;

    public $data;

    public $alreadyTaken;

    use tableImagesConnectedWithObject, hasLikes, hasComments;

    public function index(int $modelId, GetRequest $request)
    {   
        $this->data = $request->validated();

        $this->setModel($modelId);
        
        $posts = $this->model->posts()->skip($this->data['page']*$this->data['limit'])->take($this->data['limit'])->latest();

        if (($this->data['search'])){
            $posts = $posts->where('description', 'like', '%' . $this->data['search'] . '%')->get();
        } else {
            $posts = $posts->get();
        }

        return PostResource::collection($posts);
    }

    public function indexWithCommentsWithoutConnectToTheCertainGroup(GetRequestWithComments $request)
    {
        return $this->indexWithComments($request);
    }

    public function indexWithCommentsToTheCertainGroup(int $modelId, GetRequestWithComments $request)
    {
        $this->authorize('notBanned', Group::find($modelId));        
        return $this->indexWithComments($request, $modelId);
    }

    private function indexWithComments(GetRequestWithComments $request, ?int $modelId = null, $shouldValidate = true)
    {

        if ($shouldValidate){
            $this->data = $request->validated();
            $this->type = $this->getPostableType();
            
            if ($modelId){
                $this->setModel($modelId);
            }
        }
        
        $posts = Post::where('postable_type', $this->type)->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->latest()
            ->with('group');

        if (!isset($this->alreadyTaken)){
            $posts = $posts->limit($this->data['limit']['limitPosts']);
        } else {
            $posts = $posts->limit($this->data['limit']['limitPosts'] - count($this->alreadyTaken));
        }
        
        if ($this->model){
            $posts = $posts->where('postable_id', $this->model->id);
        } else {

            if ($this->type == 'group'){
                $this->data['banInTheType'] = DB::table('group_user')->where('user_id', Auth::id())->where('ban', true)->get()->pluck('group_id')->toArray();
            }

            if (count($this->data['banInTheType']) > 0){
                $posts = $posts->whereNotIn('postable_id', $this->data['banInTheType']);
            }

            $userGroups = Group::where('creator_id', Auth::id())->pluck('id');
            $this->data['end'] = new Carbon(now());
            $this->data['start'] = new Carbon(now()->subDays($this->data['daysNeedToLoad']));

            $posts = $posts->whereNotIn('postable_id' , $userGroups)->whereBetween('created_at', [$this->data['start'], $this->data['end']]);

            if ($this->data['page'] == 0){
                $this->data['youSubScribeTo'] = DB::table('group_user')->where('user_id', Auth::id())->where('ban', false)->get()->pluck('group_id')->toArray();
            }

            if (isset($this->data['youSubScribeTo']) && count($this->data['youSubScribeTo']) > 0 && !isset($this->data['youSubScribeToEnd'])){
                $posts = $posts->whereIn('postable_id', $this->data['youSubScribeTo']);
            } else {
                $posts = $posts->whereNotIn('postable_id', $this->data['youSubScribeTo']);
            }
        }
        
        if (isset($this->data['idsOfPosts'])){
            $posts = $posts->whereNotIn('id', $this->data['idsOfPosts'])->get();
        } else {
            $posts = $posts->get();
        }

        if (isset($this->alreadyTaken)){
            $posts = $this->alreadyTaken->concat($posts);
        }
        
        $posts->map(function ($post){
            
            $post->setRelation('likes', $post->likes()->latest()->take($this->data['limit']['limitLikes'])->get());
            $post->setRelation('comments', $post->comments()->latest()->take($this->data['limit']['limitComments'])->get());
            return $post;
        });

        if ($posts->count() != $this->data['limit']['limitPosts'] && !isset($this->data['youSubScribeToEnd']) && !isset($this->model)){
            $this->data['youSubScribeToEnd'] = $this->data['limit']['limitPosts'] - $posts->count();
            $this->alreadyTaken = $posts;
            return $this->indexWithComments($request, $modelId, false);
        }

        if (($this->data['page'] == 0)){

            $likedPosts = Post::whereRelation('likes', 'user_id', Auth::id());

            if ($this->model){
                $likedPosts = $likedPosts->where('postable_id', $this->model->id)->get()->pluck('id');
            } else {
                $likedPosts = $likedPosts->whereBetween('created_at', [$this->data['start'], $this->data['end']] )->get()->pluck('id');
            }

            if (isset($this->data['youSubScribeToEnd'])){
                return PostResourceWithCommentsAndLikes::collection($posts)->additional([
                    'likedPosts' => $likedPosts,
                    'youSubScribeTo'=>$this->data['youSubScribeTo'],
                    'youSubScribeToEnd'=>$this->data['youSubScribeToEnd']
                ]);
            } else {
                return PostResourceWithCommentsAndLikes::collection($posts)->additional([
                    'likedPosts' => $likedPosts,
                    'youSubScribeTo'=>$this->data['youSubScribeTo']
                ]);
            }

        } else {

            if (isset($this->data['youSubScribeToEnd'])){
                return PostResourceWithCommentsAndLikes::collection($posts)->additional([
                    'youSubScribeToEnd'=>$this->data['youSubScribeToEnd']
                ]);
            } else {
                return PostResourceWithCommentsAndLikes::collection($posts);
            }
            
        }
    }

    public function postLike(int $postId)
    {
        return $this->like($postId);
    }

    public function postDislike(int $postId)
    {
        return $this->dislike($postId);
    }

    public function getLikesOfThePost(int $postId, int $numberOflikes, GetRequestForLikes $request)
    {
        $lastCreatedAt = Carbon::parse($request->validated()['lastCreatedAt'])->format('Y-m-d H:i:s');

        $likes = $this->getLikesOfTheObject($postId, $lastCreatedAt, $numberOflikes);

        return LikeResource::collection($likes);
    }

    public function getCommentsOfThePost(int $postId, int $limitForComments, GetRequestForComments $request)
    {

        $this->authorize('notBanned', Post::find($postId));

        if (isset($request->validated()['lastCreatedAt'])){

            $lastCreatedAt = Carbon::parse($request->validated()['lastCreatedAt'])->format('Y-m-d H:i:s');
            $comments = $this->getCommentsOfTheObject($postId, $limitForComments, $lastCreatedAt);

        } else {
            $comments = $this->getCommentsOfTheObject($postId, $limitForComments);
        }
        
        return CommentResource::collection($comments);
    }

    public function createComment(int $postId, StoreRequestComment $request)
    {
        $this->authorize('notBanned', Post::find($postId));

        $createdComment = $this->comment($postId, $request->validated()['chat_message']);

        return CommentResource::make($createdComment)->additional([
            'postId' => $postId,
        ]);
    }

    public function create(int $modelId, StoreRequest $request)
    {
        $this->addPostableTypeAndIdToData($modelId, $request);

        if(isset($this->data['image'])){
            $this->image = $this->data['image'];
            unset($this->data['image']);
        }
        $post = Post::create($this->data);

        if ($this->image){
            $this->SaveMainImageInImageTable($this->image, $post->id, 'post');
        }
        
        return response()->json([
            'message' => 'post created successfully',
        ]);
    }


    public function getPostableType():string
    {        
        return getTypeService::getType('/[htt].+\/api\/(.+)(s)\//U');
    }

    public function update(int $modelId,Post $post, UpdateRequest $request)
    {
        
        $this->addPostableTypeAndIdToData($modelId, $request);

        if(array_key_exists('image', $this->data)){
            if ($this->data['image'] != null){
                $this->image = $this->data['image'];
                unset($this->data['image']);
            } else {
                $this->deleteMainImageFromTable($post);
            }
        }
        
        $post->update($this->data);
        
        if ($this->image){
            $this->deleteMainImageFromTable($post);
            $this->SaveMainImageInImageTable($this->image, $post->id, 'post');
        }

        return response()->json([
            'message' => 'post updated successfully',
        ]);
    }

    public function deleteComment(Comment $comment)
    {
        $group = Post::where('id', $comment->commentable_id)->with('group')->get()[0]->group;

        $this->authorize('admin', $group);

        $delete = $this->deleteCommentById($comment);
        if ($delete){
            return response()->json([
                'message' => 'comment deleted successfully',
            ]);
        }
    }

    public function destroy(int $modelId,Post $post)
    {
        $this->setModelAndAuthorize($modelId);
        
        $this->deleteImagesFromTable($post);

        $post->delete();

        return response()->json([
            'message' => 'post deleted successfully',
        ]);
    }

    protected function setModel(int $modelId):void
    {
        $this->type = $this->getPostableType();

        $nameOfModel = 'App\Models\\' . ucfirst($this->type);
        
        if (class_exists($nameOfModel)){
            $this->model = $nameOfModel::find($modelId);
        } else if (class_exists($nameOfModel = 'App\Models\\'.$this->type)){
            $this->model = $nameOfModel::find($modelId);
        } else {
            throw new \Exception('Invalid model');
        }

    }

    protected function setModelAndAuthorize(int $modelId):void
    {
        $this->setModel($modelId);
        $this->authorize('admin', $this->model);
    }
    
    protected function addPostableTypeAndIdToData($modelId, $request):void
    {
        $this->setModelAndAuthorize($modelId);
        $this->data = $request->validated();
        $this->data['postable_type'] = $this->type;
        $this->data['postable_id'] = $this->model->id;
    }


}