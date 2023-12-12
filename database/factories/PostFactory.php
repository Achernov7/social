<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=ChatMessage>
 */
class PostFactory extends Factory
{

    protected $postableType;

    protected $postableId;

    protected $postableCreatedAt;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'postable_type' => $this->postableType,
            'postable_id' => $this->postableId,
            'description' => fake()->text(),
            'created_at' => $this->setCreatedAt()
        ];
    }

    public function setPostableType(string $postableType)
    {
        $this->postableType = $postableType;
        return $this;
    }

    public function setPostableId(int $postableId)
    {
        $this->postableId = $postableId;
        return $this;
    }

    public function setPostableCreatedAt($postableCreatedAt)
    {
        $this->postableCreatedAt = $postableCreatedAt;
        return $this;
    }

    public function setCreatedAt()
    {
        return fake()->dateTimeBetween($this->postableCreatedAt, now());
    }

}