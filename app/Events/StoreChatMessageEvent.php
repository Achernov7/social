<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\ChatMessage\ChatMessagesResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StoreChatMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chat_message;
    private $receiver_id;
    private $Conversation_id;

    /**
     * Create a new event instance.
     */
    public function __construct($chat_message,int $receiver_id)
    {
        $this->chat_message = $chat_message;
        $this->receiver_id = $receiver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat_'.$this->receiver_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string   
    {
        return 'chat';
    }

    public function broadcastWith(): array
    {
        return ['message' => ChatMessagesResource::make($this->chat_message)->resolve()];
    }
}
