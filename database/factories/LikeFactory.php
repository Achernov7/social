<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class LikeFactory extends Factory
{

    protected string $type;

    protected int $likeable_id;

    protected $likeable_created_at;

    protected static $userIds;

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function setLikeableId(int $id)
    {
        $this->likeable_id = $id;
        return $this;
    }

    public function setLikeableCreated_at($likeable_created_at)
    {
        $this->likeable_created_at = $likeable_created_at;
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
            'likeable_type'=> $this->type,
            'likeable_id' => $this->likeable_id,
            'user_id' => $this->setUserId(),
            'created_at' => fake()->dateTimeBetween($this->likeable_created_at, now()),
        ];
    }
}
