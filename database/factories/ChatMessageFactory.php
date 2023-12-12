<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=ChatMessage>
 */
class ChatMessageFactory extends Factory
{

    public $totalUsers;

    protected $senderId;

    protected $receiverId;

    static $ConversationIds = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_message' => fake()->text(),
            'sender_id' => $this->senderId(),
            'receiver_id' => $this->receiverId(),
            'conversation_id' => $this->conversationId(),
            'created_at' => fake()->dateTimeBetween('-5 days', now()),
        ];
    }

    public function senderId()
    {
        $this->senderId = mt_rand(1, $this->totalUsers);
        return $this->senderId;
    }

    public function receiverId()
    {
        $this->receiverId = mt_rand(1, $this->totalUsers);
        if ($this->receiverId == $this->senderId) {
            return $this->receiverId();
        }

        return $this->receiverId;
    }

    public function conversationId()
    {
        if (!empty(self::$ConversationIds)){
            foreach (self::$ConversationIds as $conversationId => $senderIdAndReceiverId) {
                foreach ($senderIdAndReceiverId as $senderId => $receiverId) {
                    if (($senderId == $this->senderId && $receiverId == $this->receiverId) || ($senderId == $this->receiverId && $receiverId == $this->senderId)) {
                        return $conversationId;
                    }
                }

            }
            $conversationId = array_key_last(self::$ConversationIds)+1;
            self::$ConversationIds[$conversationId] = [$this->senderId => $this->receiverId];
            return $conversationId;
        } else {
            $conversationId = 1;
            self::$ConversationIds[$conversationId] = [$this->senderId => $this->receiverId];
            return $conversationId;
        }
    }

    public function totalUsers($users)
    {
        $this->totalUsers = $users;
        return $this;
    }
}
