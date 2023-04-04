<?php

namespace Database\Factories;

use App\WorkCase;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class WorkCaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkCase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $problem_noun = ['Silliness', 'Raccoons', 'Protestors', 'Pigeons', 'Complaining', 'Mayhem', 'Lack of service', 'Legislation', 'Issues', 'Residents', 'Graffiti', 'Zoo animals', 'Tourists', 'Officials', 'Students', 'Trash', 'Stray cats'];
        $problem_prep = ['at', 'near or around', 'every day next to'];
        $problem_place = ['the old church', 'their house', 'downtown', 'main campus', 'student union', 'city hall'];
        $problem = $problem_noun[array_rand($problem_noun)].' '.$problem_prep[array_rand($problem_prep)].' '.$problem_place[array_rand($problem_place)];

        $notes = ['Must find a way to address the issue.', 'Whatever it takes, we must resolve the question.', 'Second occurance', 'Investigate and find out the answer.', 'Get back to them ASAP.', 'Self-explanatory.'];
        $notes = str_repeat($notes[array_rand($notes)].' ', 3);

        $theteamid = App\Team::inRandomOrder()->where('app_type', '<>', 'campaign')->first()->id;
        $theteam_users = App\User::select('id')->where('current_team_id', $theteamid)->get()->toArray();
        $theteam_users = Arr::pluck($theteam_users, 'id');
        if (! $theteam_users) {
            $theteam_users = [1];
        }

        $created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 120));

        return [
            'date' => $created_at,
            'created_at' => $created_at,
            'updated_at' => $created_at,
            //'date' => $this->faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now', $timezone = 'EST'),
            'user_id' => $this->faker->randomElement($theteam_users),
            'team_id' => $theteamid,
            'subject' => $problem,
            'notes' => $notes, //$this->faker->realText($maxNbChars = 250, $indexSize = 2),
            'resolved' => rand(0, 1),
            'type' => $this->faker->randomElement(['General', 'Followup', 'Disturbance']),
        ];
    }
}
