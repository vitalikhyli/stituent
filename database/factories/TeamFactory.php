<?php

namespace Database\Factories;

use App\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $app_type = $this->faker->RandomElement(['official', 'uni', 'campaign', 'nonprofit', 'business']);
        // $app_type = $this->faker->RandomElement(['official','uni','campaign','nonprofit','business']);
        if ($app_type == 'uni') {
            $thecity = $this->faker->city;

            return [
                'name' => $thecity.' '.$this->faker->randomElement(['University', 'College']),
                'shortname' => $thecity,
                'logo_img' => $this->faker->imageUrl($width = 600, $height = 150),
                'app_type' => $app_type,
                'encryption_key' => Str::random(32),
                'billingaccount_id' => 0,
            ];
        } elseif (($app_type == 'official') || ($app_type == 'campaign')) {
            $lastname = $this->faker->lastName;

            return [
                'name' => $this->faker->randomElement(['Representative', 'Senator', 'Mayor', 'Councilor', 'Governor']).' '.$lastname,
                'shortname' => $lastname,
                'logo_img' => $this->faker->imageUrl($width = 600, $height = 150),
                'app_type' => $app_type,
                'encryption_key' => Str::random(32),
                'billingaccount_id' => 0,
            ];
        } else {
            $lastname = $this->faker->lastName;

            return [
                'name' => $this->faker->company,
                'shortname' => $this->faker->company,
                'logo_img' => $this->faker->imageUrl($width = 600, $height = 150),
                'app_type' => $app_type,
                'encryption_key' => Str::random(32),
                'billingaccount_id' => 0,
            ];
        }
    }
}
