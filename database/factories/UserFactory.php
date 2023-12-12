<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Image\PreviewImageResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Components\ImportCitiesOrTowsOfRussiaClient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $randomName;

    public $differentTowns;

    protected function makeRandomName()
    {
        $this->randomName=fake()->firstname();
        return $this->randomName;
    }

    protected function makeRandomDateUnderFourteen()
    {
        $min = strtotime("14 years ago");
        $max = strtotime("80 years ago");
        
        $rand_time = mt_rand($max, $min);
        return date("d-m-Y", $rand_time);
    }

    protected function randomRussianTown(){

        if (is_null($this->differentTowns)){
            $client = new ImportCitiesOrTowsOfRussiaClient;
            $response = $client->client->request('GET', '113');
            $allAreas = json_decode($response->getBody()->getContents(), true);
            foreach($allAreas as $region){
                if (is_array($region)){
                    foreach($region as $area){
                        foreach($area['areas'] as $town){
                            $this->differentTowns[] = $town['name'];
                        }
                    }
                }
            }
        }

        return $this->differentTowns[array_rand($this->differentTowns, 1)];   
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->makeRandomName(),
            'surname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make($this->randomName.$this->randomName),
            'birthdayDate' => $this->makeRandomDateUnderFourteen(),
            'town' => $this->randomRussianTown(),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'remember_token' => Str::random(10),
            'familyStatus' => fake()->randomElement(['additional.Choose_status','additional.Not_married','additional.Married','additional.Seeing','additional.Engaged','additional.In_Love','additional.In_civil_marriage','additional.It_s_comlicated','additional.In_active_search']),
            'about' => fake()->text(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
