<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class CommentFactory extends Factory
{

    protected string $type;

    protected int $commentable_id;

    protected $commentable_created_at;

    protected static $userIds;

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function setCommetableId(int $id)
    {
        $this->commentable_id = $id;
        return $this;
    }

    public function setCommentableCreated_at($commentable_created_at)
    {
        $this->commentable_created_at = $commentable_created_at;
        return $this;
    }

    public function setUserIds($userIds)
    {
        self::$userIds = $userIds;
        return $this;
    }

    protected function setUserId()
    {
        $arrayrandKey = array_rand(self::$userIds);
        $userIdToReturn = self::$userIds[$arrayrandKey];
        unset(self::$userIds[$arrayrandKey]);
        return $userIdToReturn;
    }

    public function definition(): array
    {
        return [
            'comment'=> fake()->paragraph(),
            'commentable_type'=> $this->type,
            'commentable_id' => $this->commentable_id,
            'user_id' => $this->setUserId(),
            'created_at' => fake()->dateTimeBetween($this->commentable_created_at, now()),
        ];
    }
}
