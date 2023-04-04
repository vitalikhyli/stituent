<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $email = $this->faker->unique()->safeEmail;

        return [
            'name' 				=> $this->faker->name,
            'email' 			=> $email,
            'username'       	=> substr($email, 0, strpos($email, '@')),
            'email_verified_at' => now(),
            'password' 			=> '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' 	=> Str::random(10),
            //Do not add fake users to Team #1 (Preset Team)
            'current_team_id' 	=> $this->faker->unique(true)->numberBetween(4, App\Team::count()),
        ];
    }
}
