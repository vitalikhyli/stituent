<?php

namespace Database\Factories;

use App\Contact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

function GenerateCall()
{
    $a = [
        'Called about', 'Inquiry:', 'Request re:', 'Complaint about', 'Comment on', 'Concern about', 'Wants answers on', 'Asked about', 'Confusion on', 'Angry with', 'Happy with',
    ];
    shuffle($a);
    $ask = array_pop($a).' ';

    $a = [
        'silliness', 'raccoons', 'protestors', 'pigeons', 'complaining', 'mayhem', 'lack of service', 'investors', 'documentary', 'professors', 'administrators', 'ducks',
    ];
    shuffle($a);
    $noun = array_pop($a).' ';

    $a = [
        'new', 'university', 'municipal', 'academic', 'fewer', 'more', 'excessive', 'problematic', 'disruptive', 'large',
    ];
    shuffle($a);
    $adj = array_pop($a).' ';

    $a = [
        'the duck pond', 'the old church', 'their house', 'downtown', 'main campus', 'student union', 'city hall',
    ];
    shuffle($a);
    $place = array_pop($a).' ';

    $a = [
        'every day', 'in the morning', 'at night',
    ];
    shuffle($a);
    $adv = array_pop($a).' ';

    $a = [
        'at', 'near', 'next to',
    ];
    shuffle($a);
    $prep = array_pop($a).' ';

    if (rand(0, 2) == 0) {
        $adj = '';
    }
    if (rand(0, 2) == 0) {
        $adv = '';
    }
    if (rand(0, 3) == 0) {
        $prep = '';
        $place .= '';
        $adv = '';
    }

    return $ask.$adj.$noun.$prep.$place.$adv;
}

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $theteamid = $this->faker->numberBetween(1, App\Team::count());
        $theteam_users = App\User::select('id')->where('current_team_id', $theteamid)->get()->toArray();
        $theteam_users = Arr::pluck($theteam_users, 'id');
        if (! $theteam_users) {
            $theteam_users = [1];
        }

        $maybe_call = 0;
        $maybe_case = 0;
        $maybe_followup = 0;
        $maybe_followup_done = 0;
        $maybe_followup_on = null;
        $maybe_private = 0;

        if (rand(0, 4) == 1) {
            $maybe_call = 1;
            $subject = $this->faker->name.' '.$this->faker->phoneNumber;

            if (rand(0, 4) == 1) {
                $maybe_private = 1;
            }
        } else {
            $subject = $this->faker->name;
        }

        if ($maybe_call != 1) {
            if (rand(0, 1) == 1) {
                $maybe_case = App\WorkCase::select('id')->where('team_id', $theteamid)->get()->toArray();
                $maybe_case = $this->faker->randomElement(Arr::pluck($maybe_case, 'id'));
            }
        }

        if (rand(0, 0) == 0) {
            $maybe_followup = 1;

            if (rand(0, 4) == 1) {
                $maybe_followup_done = 1;
            }

            if (rand(0, 4) == 1) {
                $maybe_followup_on = Carbon::now()->setTimezone('UTC')->addDays(rand(-30, 60));
            }
        }

        $created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 120));

        return [
            'date' => $created_at,
            'created_at' => $created_at,
            'updated_at' => $created_at,
            'user_id' => $this->faker->randomElement($theteam_users),
            'team_id' => $theteamid,
            'case_id' => $maybe_case,
            'type' => $this->faker->randomElement(['call', 'visit', 'email']),
            'notes' => GenerateCall(),
            'subject' => $subject,
            'category' => $this->faker->randomElement(['general', 'followup', 'disturbance']),
            'call_log' => $maybe_call,
            'private'   => $maybe_private,
            'followup'  => $maybe_followup,
            'followup_done'  => $maybe_followup_done,
            'followup_on'  => $maybe_followup_on,
        ];
    }
}
