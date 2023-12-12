<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{

    protected $totalUsers;

    protected $links = [
        "https://www.google.com/search?q=faker+for+web+links&sca_esv=561317309&sxsrf=AB5stBiyPyHnJT7m8yBaJZtOioxS18QUAg%3A1693403624563&ei=6EnvZLyCIsnawPAPzLefsAg&ved=0ahUKEwi8loOMxISBAxVJLRAIHczbB4YQ4dUDCA8&uact=5&oq=faker+for+web+links&gs_lp=Egxnd3Mtd2l6LXNlcnAiE2Zha2VyIGZvciB3ZWIgbGlua3MyCBAhGKABGMMESNoHUMsBWMsBcAF4AZABAJgBcaABcaoBAzAuMbgBA8gBAPgBAcICChAAGEcY1gQYsAPiAwQYACBBiAYBkAYI&sclient=gws-wiz-serp",
        "https://dzen.ru/?yredirect=true",
        "https://mail.ru/",
        "https://www.youtube.com/",
        "https://www.twitch.tv/",
        "https://www.instagram.com/",
        "https://vk.com/",
        "https://www.facebook.com/",
        "https://twitter.com/",
        "https://www.linkedin.com/",
        "https://www.pinterest.com/",
        "https://www.reddit.com/",
        "https://www.tiktok.com/",
        "https://www.quora.com/",
        "https://www.whatsapp.com/",
        "https://stackoverflow.com/",
        "https://www.deviantart.com/",
        "https://www.behance.net/",
        "https://www.flickr.com/",
        "https://www.youtube.com/",
        "https://www.tumblr.com/",
        "https://www.dribbble.com/",
        "https://www.deviantart.com/",
        "https://laracast.com/",
    ];

    protected function randomLinks(){
        $randomValues = array_intersect_key($this->links, array_flip(array_rand($this->links, mt_rand(2, 5))));
        return implode(',', $randomValues);
    }
        /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function setTotalUsers(int $users)
    {
        $this->totalUsers = $users;
        return $this;
    }

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->text(),
            'links' => $this->randomLinks(),
            'creator_id' => rand(1, $this->totalUsers),
            'created_at' => fake()->dateTimeBetween('-10 days', now()),
        ];
    }
}
