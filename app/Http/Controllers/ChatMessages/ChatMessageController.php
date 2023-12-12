<?php

namespace App\Http\Controllers\ChatMessages;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\StoreChatMessageEvent;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\ChatMessage\GetRequest;
use App\Http\Requests\ChatMessage\StoreRequest;
use App\Http\Requests\ChatMessage\IndexGetRequest;
use App\Http\Resources\ChatMessage\ChatMessagesResource;
use App\Http\Resources\ChatMessage\ListOfMessagesResource;
use App\Http\Resources\ChatMessage\UsersInformationForChatResource;
use App\Components\SharedServices\getNameWithSurnameFromSearchingUser;

class ChatMessageController extends Controller
{
    public function index(IndexGetRequest $request)
    {
        $data = $request->validated();
        
            if (isset($data['DialogIds'])) {
                $DialogIds = $data['DialogIds'];
            } else {
                $DialogIds =[];
                Auth::user()->update(['stop_check_chat_messages' => false]);
            }


            if ($data['searchingUser'] == null) {
                
                $findUniqueDialogsOfUser = ChatMessage::query()
                    ->select(DB::raw(' t.*'))
                    ->from(DB::raw('(SELECT * FROM (SELECT * FROM chat_messages WHERE receiver_id = ' . auth()->user()->id . ' OR sender_id = ' . auth()->user()->id . ' ORDER BY created_at DESC) GROUP BY conversation_id ) AS t'));
            } else {
                
                if (str_contains($data['searchingUser'], ' ') || str_contains($data['searchingUser'], '&nbsp;')){
                    
                    $nameOrSuraname = getNameWithSurnameFromSearchingUser::getNameSurname($data['searchingUser']);

                    if (gettype($nameOrSuraname) == 'string'){
                        
                        $findUniqueDialogsOfUser = ChatMessage::query()
                        ->select(DB::raw(' t.*'))
                        ->from(DB::raw("(SELECT * FROM (SELECT chat_messages.*, sender.name AS sender_name, sender.surname AS sender_surname, receiver.name AS receiver_name, receiver.surname AS receiver_surname FROM chat_messages LEFT JOIN users AS sender ON chat_messages.sender_id = sender.id LEFT JOIN users AS receiver ON chat_messages.receiver_id = receiver.id
                            WHERE (receiver_id = " . auth()->user()->id . " AND (UPPER (sender_name) LIKE UPPER ('%' || ? || '%') OR UPPER (sender_surname) LIKE UPPER ('%' || ? || '%')))  OR ( sender_id = " . auth()->user()->id . " AND (UPPER (receiver_name) LIKE UPPER ('%' || ? || '%') OR UPPER (receiver_surname) LIKE UPPER ('%' || ? || '%'))) ORDER BY created_at DESC) 
                            GROUP BY conversation_id ) AS t"))
                        ->setBindings([$nameOrSuraname, $nameOrSuraname, $nameOrSuraname, $nameOrSuraname]);

                    } else if (gettype($nameOrSuraname) == 'array'){
                        
                        $findUniqueDialogsOfUser = ChatMessage::query()
                        ->select(DB::raw(' t.*'))
                        ->from(DB::raw("(SELECT * FROM (SELECT chat_messages.*, sender.name AS sender_name, sender.surname AS sender_surname, receiver.name AS receiver_name, receiver.surname AS receiver_surname FROM chat_messages LEFT JOIN users AS sender ON chat_messages.sender_id = sender.id LEFT JOIN users AS receiver ON chat_messages.receiver_id = receiver.id
                            WHERE (receiver_id = " . auth()->user()->id . " AND ((UPPER (sender_name) LIKE UPPER ('%' || ? || '%') AND UPPER (sender_surname) LIKE UPPER ('%' || ? || '%')) OR (UPPER (sender_name) LIKE UPPER ('%' || ? || '%') AND UPPER (sender_surname) LIKE UPPER ('%' || ? || '%')) ))  OR ( sender_id = " . auth()->user()->id . " AND ((UPPER (receiver_name) LIKE UPPER ('%' || ? || '%') AND UPPER (receiver_surname) LIKE UPPER ('%' || ? || '%')) OR (UPPER (receiver_name) LIKE UPPER ('%' || ? || '%') AND UPPER (receiver_surname) LIKE UPPER ('%' || ? || '%')) )) ORDER BY created_at DESC) 
                            GROUP BY conversation_id ) AS t"))
                        ->setBindings([$nameOrSuraname[0], $nameOrSuraname[1], $nameOrSuraname[1], $nameOrSuraname[0], $nameOrSuraname[1], $nameOrSuraname[0], $nameOrSuraname[0], $nameOrSuraname[1]]);
                    }

                } else {

                    $findUniqueDialogsOfUser = ChatMessage::query()
                        ->select(DB::raw(' t.*'))
                        ->from(DB::raw("(SELECT * FROM (SELECT chat_messages.*, sender.name AS sender_name, sender.surname AS sender_surname, receiver.name AS receiver_name, receiver.surname AS receiver_surname FROM chat_messages LEFT JOIN users AS sender ON chat_messages.sender_id = sender.id LEFT JOIN users AS receiver ON chat_messages.receiver_id = receiver.id
                            WHERE (receiver_id = " . auth()->user()->id . " AND (UPPER (sender_name) LIKE UPPER ('%' || ? || '%') OR UPPER (sender_surname) LIKE UPPER ('%' || ? || '%')))  OR ( sender_id = " . auth()->user()->id . " AND (UPPER (receiver_name) LIKE UPPER ('%' || ? || '%') OR UPPER (receiver_surname) LIKE UPPER ('%' || ? || '%'))) ORDER BY created_at DESC) 
                            GROUP BY conversation_id ) AS t"))
                        ->setBindings([$data['searchingUser'], $data['searchingUser'], $data['searchingUser'], $data['searchingUser']]);
                }


            }

            $findUniqueDialogsOfUser = $findUniqueDialogsOfUser->whereNotIn('conversation_id', $DialogIds)->limit($data['limit'])->orderBy('created_at', 'desc')->get();

            return ListOfMessagesResource::collection($findUniqueDialogsOfUser)->additional(['SearchingUser' => $data['searchingUser']]); 
    }

    public function indexCheck(IndexGetRequest $request)
    {
        
        $timeToStop = time()+7;
        $data = $request->validated();

        set_time_limit($timeToStop + 10);
        while(time() < $timeToStop) {

            if (User::find(auth()->user()->id)->stop_check_chat_messages == true) {
                  
                Auth::user()->update(['stop_check_chat_messages' => false]);
                return response()->json([
                    'stop_check_messages' => 'stop_check_chat_messages',
                ]);
            }

            if ($data['searchingUser'] == null) {
                $latestTimestampInDataBase = ChatMessage::latest('created_at')->where('receiver_id', auth()->user()->id)->orWhere('sender_id', auth()->user()->id)->value('created_at');
            } else {

                if (str_contains($data['searchingUser'], ' ') || str_contains($data['searchingUser'], '&nbsp;')){
                    $nameOrSuraname = getNameWithSurnameFromSearchingUser::getNameSurname($data['searchingUser']);

                    if (gettype($nameOrSuraname) == 'string'){
                        
                        $latestTimestampInDataBase =ChatMessage::where(function ($query) use ($nameOrSuraname) {
                            $query->orWhere(function ($query) use ($nameOrSuraname) {
                                $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                    $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname . '%')->orWhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname . '%');
                                });
                            })->orWhere(function ($query) use ($nameOrSuraname) {
                                $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                    $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname . '%')->orWhereRelation('receiver', 'surname', 'LIKE', '%' . $nameOrSuraname . '%');
                                });
                            });
                        })
                        ->latest()
                        ->value('created_at');

                    } else if (gettype($nameOrSuraname) == 'array'){
                        
                        $latestTimestampInDataBase =ChatMessage::where(function ($query) use ($nameOrSuraname) {
                            $query->orWhere(function ($query) use ($nameOrSuraname) {
                                $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                    $query->where(function($query) use ($nameOrSuraname) {
                                        $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname[0] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[1] . '%');
                                    })
                                    ->orWhere(function($query) use ($nameOrSuraname) {
                                        $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname[1] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[0] . '%');
                                    });
                                });
                            })->orWhere(function ($query) use ($nameOrSuraname) {
                                $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                    $query->where(function($query) use ($nameOrSuraname) {
                                        $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname[0] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[1] . '%');
                                    })
                                    ->orWhere(function($query) use ($nameOrSuraname) {
                                        $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname[1] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[0] . '%');
                                    });
                                });
                            });
                        })
                        ->latest()
                        ->value('created_at');

                    } 
                } else {

                    $latestTimestampInDataBase =ChatMessage::where(function ($query) use ($data) {
                        $query->orWhere(function ($query) use ($data) {
                            $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($data) {
                                $query->whereRelation('sender', 'name', 'LIKE', '%' . $data['searchingUser'] . '%')->orWhereRelation('sender', 'surname', 'LIKE', '%' . $data['searchingUser'] . '%');
                            });
                        })->orWhere(function ($query) use ($data) {
                            $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($data) {
                                $query->whereRelation('receiver', 'name', 'LIKE', '%' . $data['searchingUser'] . '%')->orWhereRelation('receiver', 'surname', 'LIKE', '%' . $data['searchingUser'] . '%');
                            });
                        });
                    })
                    ->latest()
                    ->value('created_at');
                }
            }

            if ($latestTimestampInDataBase != null){
                
                if ($data['latestTimestamp'] != $latestTimestampInDataBase->timestamp) {

                    sleep(1);
                    $dataTimestamp = Carbon::createFromTimestamp( $data['latestTimestamp']);

                    if ($data['searchingUser'] == null) {
                        $findNewMessages = ChatMessage::where(function ($query) {
                                $query->where('receiver_id', auth()->user()->id)->orWhere('sender_id', auth()->user()->id);
                            })
                            ->limit($data['limit'])
                            ->where('created_at', '>', $dataTimestamp)->get();
                    } else {

                        if (isset($nameOrSuraname)){
                            if (gettype($nameOrSuraname) == 'array'){

                                $findNewMessages = ChatMessage::where(function ($query) use ($nameOrSuraname) {
                                    $query->orWhere(function ($query) use ($nameOrSuraname) {
                                        $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                            $query->where(function($query) use ($nameOrSuraname){
                                                $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname[0] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[1] . '%');
                                            })->orWhere(function($query) use ($nameOrSuraname){
                                                $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname[1] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[0] . '%');
                                            });
                                        });
                                    })->orWhere(function ($query) use ($nameOrSuraname) {
                                        $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                            $query->where(function($query) use ($nameOrSuraname){
                                                $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname[0] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[1] . '%');
                                            })->orWhere(function($query) use ($nameOrSuraname){
                                                $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname[1] . '%')->WhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname[0] . '%');
                                            });
                                        });
                                    });
                                })
                                ->limit($data['limit'])
                                ->where('created_at', '>', $dataTimestamp)->get();

                            } else if (gettype($nameOrSuraname) == 'string'){
                                
                                $findNewMessages = ChatMessage::where(function ($query) use ($nameOrSuraname) {
                                    $query->orWhere(function ($query) use ($nameOrSuraname) {
                                        $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                            $query->whereRelation('sender', 'name', 'LIKE', '%' . $nameOrSuraname . '%')->orWhereRelation('sender', 'surname', 'LIKE', '%' . $nameOrSuraname . '%');
                                        });
                                    })->orWhere(function ($query) use ($nameOrSuraname) {
                                        $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($nameOrSuraname) {
                                            $query->whereRelation('receiver', 'name', 'LIKE', '%' . $nameOrSuraname . '%')->orWhereRelation('receiver', 'surname', 'LIKE', '%' . $nameOrSuraname . '%');
                                        });
                                    });
                                })
                                ->limit($data['limit'])
                                ->where('created_at', '>', $dataTimestamp)->get();

                            }
                        } else {

                            $findNewMessages = ChatMessage::where(function ($query) use ($data) {
                                    $query->orWhere(function ($query) use ($data) {
                                        $query->where('receiver_id', auth()->user()->id)->where(function ($query) use ($data) {
                                            $query->whereRelation('sender', 'name', 'LIKE', '%' . $data['searchingUser'] . '%')->orWhereRelation('sender', 'surname', 'LIKE', '%' . $data['searchingUser'] . '%');
                                        });
                                    })->orWhere(function ($query) use ($data) {
                                        $query->where('sender_id', auth()->user()->id)->where(function ($query) use ($data) {
                                            $query->whereRelation('receiver', 'name', 'LIKE', '%' . $data['searchingUser'] . '%')->orWhereRelation('receiver', 'surname', 'LIKE', '%' . $data['searchingUser'] . '%');
                                        });
                                    });
                                })
                                ->limit($data['limit'])
                                ->where('created_at', '>', $dataTimestamp)->get();
                        }

                    }
                    
                    return ListOfMessagesResource::collection($findNewMessages)->additional(['SearchingUser' => $data['searchingUser']]);

                }
            }

            sleep(1);
        }
    }

    public function stopCheck()
    {
        Auth::user()->update(['stop_check_chat_messages' => true]);

        return response()->json([
            'message' => 'stop_check_chat_messages',
        ]);
    }

    public function store(int $receiver_id, StoreRequest $request)
    {
        if (Gate::denies('valid-receiver-id', $receiver_id)) {
            return response()->json([
                'messages' => 'reciever_id is not valid',
            ]);
        }

        $data = $request->validated();
        $data['sender_id'] = auth()->user()->id;
        $data['receiver_id'] = $receiver_id;

        $ConversationId = $this->FindDialog($receiver_id);

        if ($ConversationId) {
            $data['conversation_id'] = $ConversationId;
        } else {
            $data['conversation_id'] = ChatMessage::max('conversation_id') + 1;
        }

        $data = ChatMessage::create($data);

        broadcast(new StoreChatMessageEvent($data, $receiver_id));

        return response()->json([
            'message' => $data['chat_message'],
            'time'=> $data['created_at']->format('H:i'),
        ]);
    }

    public function show(int $receiver_id, GetRequest $request)
    {

        if (Gate::denies('valid-receiver-id', $receiver_id)) {
            return response()->json([
                'messages' => 'reciever_id is not valid',
            ]);
        }

        $data = $request->validated();

        if (isset($data['IdsOfMessages'])) {
            $IdsOfMessages = $data['IdsOfMessages'];
        } else {
            $IdsOfMessages = [];
        }
        
        $ConversationId = $this->FindDialog($receiver_id);

        $dateOfReceiverActivity = Redis::get('usersLastOnline:'.$receiver_id);
         
        if ($dateOfReceiverActivity) {
            
            if (Carbon::now()->lt( Carbon::parse($dateOfReceiverActivity)->addMinutes(1)) ) {
                $lastReceiverActivity = 'Online';
            } else {
                $lastReceiverActivity = Carbon::parse($dateOfReceiverActivity)->diffForHumans();
            }
        } else {
            $lastReceiverActivity = User::find($receiver_id)->last_activity;
            
            if ($lastReceiverActivity) {
                $lastReceiverActivity = Carbon::parse($lastReceiverActivity)->diffForHumans();
            }
        }
        
        if ($ConversationId) {

            $messages = ChatMessage::query()
                ->select(DB::raw(' t.*'))
                ->from(DB::raw('(SELECT * FROM chat_messages WHERE conversation_id = ' . $ConversationId . ' AND id NOT IN (' .implode(',', $IdsOfMessages). ') ORDER BY created_at DESC LIMIT ' . $data['limit'] . ' ) AS t'))
                ->orderBy('id', 'asc')
                ->get();

            if ($data['page'] == 0) {

                if (isset($data['date'])) {
                    $data['date'] = Carbon::parse($data['date'])->addMinutes(80);
                    $diffInMinutesBetweenServerTimeAndUser = Carbon::now()->diffInMinutes($data['date']);

                    if ( ($diffInMinutesBetweenServerTimeAndUser <60) )  {
                        $diffInDates = null;
                    } else {
                        $diffInDates = $data['date']->diffInHours(Carbon::now());

                        if (($data['date']->gt(Carbon::now()))) {
                            $diffInDates = -$diffInDates;
                        } 
                    }
                }

                ChatMessage::where('conversation_id', $ConversationId)->where('receiver_id', auth()->user()->id)->where(function ($query) {
                    $query->where('receiver_saw', 0)->orWhere('receiver_saw', null);
                })->update(['receiver_saw' => 1]);

                return ChatMessagesResource::collection($messages)->additional([
                    'Sender'=> UsersInformationForChatResource::make(auth()->user()),
                    'Receiver'=> UsersInformationForChatResource::make(User::find($receiver_id)),
                    'lastReceiverActivity' => $lastReceiverActivity,
                    'diffInhours' => $diffInDates
                ]);
            } else {

                return ChatMessagesResource::collection($messages);
            }

        } else {
            $messages = [];
            return ChatMessagesResource::collection($messages)->additional([
                'Sender'=> UsersInformationForChatResource::make(auth()->user()),
                'Receiver'=> UsersInformationForChatResource::make(User::find($receiver_id))
            ]);
        }
    }

    public function receiverSaw($idOfMessage)
    {
        if (ChatMessage::where('id', $idOfMessage)->exists()) {
            if (ChatMessage::where('id', $idOfMessage)->first()->receiver_id == auth()->user()->id) {
                ChatMessage::where('id', $idOfMessage)->update(['receiver_saw' => 1]);
                return response()->json([
                    'messages' => 'receiver_saw',
                ]);
            } else {
                return response()->json([
                    'messages' => 'reciever_id is not valid',
                ]);
            }
        }
    }

    public function getNumberOfUnreadConversationsPlusDeafultAudioPlusId()
    {
        $numberOfUnreadConversations = $this->getNumberOfUnreadConversations();

        return response()->json([
            'numberOfUnreadConversations' => $numberOfUnreadConversations,
            'UserId' => auth()->user()->id,
            'messageSound' => url('/storage/music/default_music/Default_message.mp3'),
        ]);
    }

    public function getNumberOfUnreadConversations():int
    {
        return ChatMessage::query()
            ->select(DB::raw(' t.*'))
            ->from(DB::raw('(SELECT * FROM (SELECT * FROM chat_messages WHERE receiver_id = ' . auth()->user()->id . ' ORDER BY created_at DESC ) GROUP BY conversation_id ) AS t'))
            ->where('receiver_saw', null)
            ->get()
            ->count();
    }

    public function userRead(int $conversationId)
    {
        ChatMessage::where('receiver_id', auth()->user()->id)->where('conversation_id', $conversationId)->update(['receiver_saw' => 1]);
        return response()->json([
            'message' => 'user successfully read',
        ]);
    }

    protected function FindDialog(int $receiver_id)
    {
        $firstFindOfUsersDialog = ChatMessage::where(function ($query) use ($receiver_id) {
            $query->where('receiver_id', $receiver_id)->where('sender_id', auth()->user()->id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', auth()->user()->id);
        })->first();

        if ($firstFindOfUsersDialog){
            return $firstFindOfUsersDialog->conversation_id;
        } else {
            return null;
        }
    }
}