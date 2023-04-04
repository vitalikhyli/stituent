<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

use App\Models\Campaign\Volunteer;
use App\CampaignParticipant;

class ConvertVolunteers extends Command
{

    protected $signature = 'cf:convert_volunteers';
    protected $description = 'Bring over to new format';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fields = [];

        $attributes = CampaignParticipant::first()->getAttributes();

        foreach($attributes as $key => $field) {
            if (str_starts_with($key, 'volunteer_')) {
                $fields[] = str_replace('volunteer_', '', $key);
            }
        }

        foreach(CampaignParticipant::all() as $cp) {

            $v = Volunteer::where('participant_id', $cp->participant_id)->first();

            if (!$v) {
                $v = new Volunteer;
            }

            $types = $v->types ?? [];

            foreach($fields as $field) {
                if ($cp->{ 'volunteer_'.$field }) {
                    $types[] = $field;
                }
            }
            
            $v->types           = collect($types)->unique()->toArray();

            if (!$v->types) {
                continue;   // Not a volunteer (no checkboxes)
            }

            $v->team_id         = $cp->team_id;
            $v->participant_id  = $cp->participant_id;
            $v->voter_id        = $cp->voter_id;
            if ($cp->participant) {
                $v->username = $cp->participant->full_name;
                $attributes = $cp->participant->getAttributes(); // Accessor screws it up?
                $email = $attributes['primary_email'];
                if ($email == '') {
                    $email = null;
                }
                $v->email = $email;
            }
            $v->save();
        }
    }
}
