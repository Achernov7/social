<?php

namespace App\Http\Gates\ChatMessageGate;

use App\Models\User;


class ChatMessageGate 
{
    public function validReceiverId (User $user, $receiver_id) {
        return $user->id != $receiver_id && User::find($receiver_id);
    }
}