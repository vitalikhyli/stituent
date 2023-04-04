<?php

namespace App\Console\Commands\Campaign;

use App\Participant;
use App\Team;
use Illuminate\Console\Command;

class AddPersonID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:add_person_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds all Participants that have a person_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $team_ids = Participant::select('team_id')->distinct()->pluck('team_id');
        $teams = Team::whereIn('id', $team_ids)->get();

        foreach ($teams as $team) {
            $office_team = $team->account->teams()->where('app_type', 'office')->first();
            if ($office_team) {
                $participants = $team->participants()->whereNull('crossteam_person_id')->get();
                //dd($participants);
                echo $team->name.': Checking '.$participants->count()." participants.\n";

                $people = $office_team->people()->whereNotNull('voter_id')->get()->keyBy('voter_id');
                //dd($people);
                foreach ($participants as $participant) {
                    //echo $participant->voter_id."\n";
                    if (isset($people[$participant->voter_id])) {
                        $person = $people[$participant->voter_id];
                        $participant->crossteam_person_id = $person->id;
                        $participant->save();
                        echo $team->name.': Updated '.$participant->name.', voter ID '.$participant->voter_id."\n";
                    }
                }
            }
        }
    }
}
