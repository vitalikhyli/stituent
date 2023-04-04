<?php

namespace Database\Factories;

use App\WorkFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkFile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $the_ext = $this->faker->randomElement(['pdf', 'doc']);
        $the_name = $this->faker->firstName;

        return [
            'name' => $the_name.'.'.$the_ext,
            'path' => '2019/05/scuOllpqGbIzw1K5tzuaHBscFNMpTPUQaLTXF7Au.png',
            'user_id' => $this->faker->unique(true)->numberBetween(1, App\User::count()),
            'team_id' => $this->faker->unique(true)->numberBetween(1, App\Team::count()),
            'ext' => $the_ext,
            'type' => null,
        ];
    }
}
