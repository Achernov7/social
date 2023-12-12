<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Image;
use App\Models\Music;
use App\Models\Comment;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class DatabaseSeeder extends Seeder
{

    protected $randomIdsToAttach = [];

    protected $alreadyAttached = [];

    public function IdsToAttachForUsers($numberOfUsers, $user,int $max = 20)
    {

        $this->randomIdsToAttach = [];

        do {
            $randomIdToAttach = rand(1, $numberOfUsers);
            if (!in_array($randomIdToAttach, $this->randomIdsToAttach) && $user->id != $randomIdToAttach) {
                
                if (count($this->alreadyAttached) == 0) {
                    if (!in_array($randomIdToAttach, $this->randomIdsToAttach)) {
                        array_push($this->randomIdsToAttach, $randomIdToAttach);
                    }
                } else {
                    $lastElement = end($this->alreadyAttached);
                    foreach ($this->alreadyAttached as $key=>$IdsThatUserAttached) {
                        if ($key == $randomIdToAttach) {
                            if (!in_array($user->id, $IdsThatUserAttached)) {
                                array_push($this->randomIdsToAttach, $randomIdToAttach);
                                break;
                            } else {
                                break;
                            }
                        }
                        if ($IdsThatUserAttached == $lastElement) {
                            if ($key == $randomIdToAttach) {
                                if (!in_array($user->id, $IdsThatUserAttached)) {
                                    array_push($this->randomIdsToAttach, $randomIdToAttach);
                                }
                            } else {
                                array_push($this->randomIdsToAttach, $randomIdToAttach);
                            }
                        }
                    }

                }
            }
        } while (count($this->randomIdsToAttach) < $max);

        $this->alreadyAttached[$user->id] = $this->randomIdsToAttach;

        $randomIdsToAttachWithAddedAccepted = [];
        
        foreach ($this->randomIdsToAttach as $key=>$id) {
            if ($key<10) {
                $user = [
                    'friend_id' => $id,
                    'accepted' => 1
                ];
            } else {
                $user = [
                    'friend_id' => $id,
                    'accepted' => 0
                ];
            }
            array_push($randomIdsToAttachWithAddedAccepted, $user);
        }

        $this->randomIdsToAttach = $randomIdsToAttachWithAddedAccepted;

        return $this->randomIdsToAttach;

    }

    public function randomUserIdsToAttch(int $totalNumberOfUsers, int $numberOfUsersToAttach, $addUserToRandomIds = null):array
    {
        $this->randomIdsToAttach = [];

        if ($addUserToRandomIds != null) {
            array_push($this->randomIdsToAttach, $addUserToRandomIds);
        }

        do {
            $randomIdToAttach = rand(1, $totalNumberOfUsers);
            if (!in_array($randomIdToAttach, $this->randomIdsToAttach)) {
                array_push($this->randomIdsToAttach, $randomIdToAttach);
            }
        } while (count($this->randomIdsToAttach) < $numberOfUsersToAttach);

        return $this->randomIdsToAttach;
    }

    public function assignImagesToObjects($objectId, $objectType): void
    {
        $randomPicture = mt_rand(1, 5);
        Image::create(
            [
                'path' => null,
                'url' => null,
                'mini_url' => '/storage/images/examples/mini_' . $randomPicture.'.jpg',
                'preview_url' => '/storage/images/examples/default_' . $randomPicture.'.jpg',
                'micro_url' => '/storage/images/examples/micro_' . $randomPicture.'.jpg',
                'imageable_id' => $objectId,
                'imageable_type' => $objectType,
                'is_main_image' => true
            ]
        );
    }


    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $numberOfUsers = 80;
        User::factory($numberOfUsers)->create()->each(function ($user) use ($numberOfUsers) {
            $this->assignImagesToObjects($user->id, 'user');
            $user->SubscribeToUser()->attach($this->IdsToAttachForUsers($numberOfUsers,$user));
        });


        ChatMessage::factory(3000)->totalUsers($numberOfUsers)->create();
        for ($i=0; $i < $numberOfUsers ; $i++) { 
            $UsersLastMessagesInEachConversation = ChatMessage::query()
                ->select(DB::raw(' t.*'))
                ->from(DB::raw('(SELECT * FROM (SELECT * FROM chat_messages WHERE sender_id = ' . $i . ' ORDER BY created_at DESC ) GROUP BY conversation_id ) AS t'))
                ->get();

            foreach ($UsersLastMessagesInEachConversation as $Conversation) {
                ChatMessage::where('conversation_id', $Conversation->conversation_id)->where('receiver_id', $i)->where('created_at', '<', $Conversation->created_at)->update([
                    'receiver_saw' => 1
                ]);
            }
        }


        Group::factory(40)->setTotalUsers($numberOfUsers)->create()->each(function ($group) use ($numberOfUsers) {
            $this->assignImagesToObjects($group->id, 'group');

            $group->users()->attach($this->randomUserIdsToAttch( $numberOfUsers, 37, $group->creator_id));

            Post::factory((mt_rand(7, 30)))->setPostableType('group')->setPostableId($group->id)->setPostableCreatedAt($group->created_at)->create()->each(function ($post) {
                $shouldThePostHaveImage = mt_rand(1, 5);
                if ($shouldThePostHaveImage < 5) {
                    $this->assignImagesToObjects($post->id, 'post');
                }

                $needCommentForPost = mt_rand(1, 5);
                if ($needCommentForPost < 5) {
                    Comment::factory((mt_rand(1, 15)))->setType('post')->setCommetableId($post->id)->setCommentableCreated_at($post->created_at)->setUserIds($this->randomIdsToAttach)->create();
                }

                $needLikeForPost = mt_rand(1, 20);
                if ($needLikeForPost < 20) {
                    Like::factory((mt_rand(1, 20)))->setType('post')->setLikeableId($post->id)->setLikeableCreated_at($post->created_at)->setUserIds($this->randomIdsToAttach)->create();
                }
            });
        
        });

        Music::factory(80)->create()->each(function ($music) use ($numberOfUsers) {
            $music->users()->attach($this->randomUserIdsToAttch($numberOfUsers,  20));
        });
        

    }
}
