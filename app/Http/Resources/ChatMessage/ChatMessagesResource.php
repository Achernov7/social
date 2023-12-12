<?php

namespace App\Http\Resources\ChatMessage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Friend\FriendResource;
use Illuminate\Http\Resources\Json\JsonResource;


class ChatMessagesResource extends JsonResource
{
    public function time()
    {
        $now = new \DateTime();
        $now=\Carbon\Carbon::createFromFormat('d:m:Y', $now->format('d:m:Y'));
        $created_at = \Carbon\Carbon::createFromFormat('d:m:Y', $this->created_at->format('d:m:Y'));
        
        if ($now->diffInDays($created_at) > 0) {

            return $this->created_at->diffForHumans();
        }

        return $this->created_at->format('H:i');
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'=> $this->id,
            'chat_message' => $this->chat_message,
            'time'=> $this->time(),
            'conversation_id' => $this->conversation_id,
            'whose_message' => Auth::user()->id == $this->sender_id ? 'this is senders message' : 'this is receivers message',
            'senderInfo' => FriendResource::make(Auth::user()),
        ];
    }
}
