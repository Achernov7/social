<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class MusicFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => fake()->name().'-'.fake()->word(),
            'path' => 'music/examples/example'.mt_rand(1, 8).'.mp3',
            'url' => 'storage/music/examples/example'.mt_rand(1, 8).'.mp3',
        ];
    }
}
