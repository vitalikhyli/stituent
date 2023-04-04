<?php

namespace App\Console\Commands\Campaign;

use App\CampaignParticipant;
use App\ParticipantTag;
use App\Team;
use Illuminate\Console\Command;

class AddVoterID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:add_voter_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds all Campaign Participants / Tags that have no voter_id';

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
        $cps = CampaignParticipant::whereNull('voter_id')->with('participant', 'team')->get();
        echo 'Updating '.$cps->count()." Campaign participant Voter IDs\n";
        foreach ($cps as $cp) {
            $cp->voter_id = $cp->participant->voter_id;
            $cp->save();
            echo $cp->team->name.': Saved '.$cp->participant->voter_id.', '.$cp->participant->name."\n";
        }

        $pts = ParticipantTag::whereNull('voter_id')->with('participant', 'team')->get();
        echo 'Updating '.$pts->count()." Participant Tag Voter IDs\n";
        foreach ($pts as $pt) {
            if ($pt->participant) {
                $pt->voter_id = $pt->participant->voter_id;
                $pt->save();
                echo $pt->team->name.': Saved '.$pt->participant->voter_id.', '.$pt->participant->name."\n";
            } else {
                echo $pt->team->name.': ERROR - Particiapnt ID Missing, '.$pt->participant_id."\n";
            }
        }
    }
}
