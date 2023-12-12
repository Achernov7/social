<?php

namespace App\Traits;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Components\SharedServices\getTypeService;

Trait hasComments
{    
    public function comment(int $objectId,string $comment)
    {
        $type = $this->getCommentableType();
        
        return Comment::create([
            'commentable_id' => $objectId,
            'commentable_type' => $type,
            'user_id' => Auth::id(),
            'comment' => $comment
        ]);
    }

    public function getCommentsOfTheObject(int $objectId, int $limit, $lastCreatedAt = null)
    {
        $type = $this->getCommentableType();
        
        $comments = Comment::where('commentable_id', $objectId)->where('commentable_type', $type)->take($limit)->latest()->with('user');

        if ($lastCreatedAt == !null){
            $comments = $comments->where('created_at', '<', $lastCreatedAt)->get();
        } else {
            $comments = $comments->get();
        }
        
        return $comments;
    }

    public function deleteCommentById(Comment $comment)
    {
        return $comment->delete();
    }

    private function getCommentableType()
    {
        return getTypeService::getType("/.+\/(.+)(s)\/comments/");
    }
}