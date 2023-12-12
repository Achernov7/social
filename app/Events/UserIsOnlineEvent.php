<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\ChatMessage\ChatMessagesResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserIsOnlineEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user_is_online_'.Auth::user()->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string   
    {
        return 'user_is_online';
    }

    public function broadcastWith(): array
    {
        return ['message' => 'User is online'];
    }
}
