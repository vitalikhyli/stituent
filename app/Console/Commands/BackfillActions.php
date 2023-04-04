<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Action;
use App\CampaignParticipant;
use App\ParticipantTag;
use App\Participant;
use App\Donation;

class BackfillActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:backfill_actions {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through campaign activity and converts to actions.';

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
     * @return int
     */
    public function handle()
    {
        $already_added = Action::select('participant_id')->take(5)->get()->keyBy('participant_id');

        if ($this->option('clear')) {
            Action::truncate();
        }

        $cps = CampaignParticipant::all();
        foreach ($cps as $cp) {

            $added = false;
            if ($cp->isVolunteer()) {
                addActionFromObject($cp, 'Volunteering', $cp->getVolunteering(), null);
                $added = true;
            }
            if ($cp->support > 0) {
                addActionFromObject($cp, 'Support '.$cp->support, $cp->getSupportName(), null);
                $added = true;
            }
            if ($cp->notes) {
                addActionFromObject($cp, 'Added Note', $cp->notes, 'updated_at');
                $added = true;
            }
            if (!$added) {
                addActionFromObject($cp, 'Updated', 'Voter became a Participant.', null);
            }
        }
        


        $ptags = ParticipantTag::all();
        foreach ($ptags as $ptag) {
            addActionFromObject($ptag, "Tagged", $ptag->tag->name, null);
        }

        $donations = Donation::all();
        echo "Adding ".$donations->count()." Donations.\n";
        foreach ($donations as $donation) {
            $action = addActionFromObject($donation, "Contributed", $donation->amount, null);
        }

        $already_added = Action::select('participant_id')->get()->keyBy('participant_id');
        //dd($already_added);
        $participants = Participant::all();
        $participants_to_add = collect([]);
        foreach ($participants as $p) {
            if (!isset($already_added[$p->id])) {
                $participants_to_add[] = $p;
            }
        }

        //dd($already_added->count(), $participants_to_add->count(), Participant::all()->count());
        echo "Adding ".$participants_to_add->count()." Participants.\n";
        foreach ($participants_to_add as $participant) {
            $str = "";
            if ($participant->primary_phone) {
                $str .= "Added Phone. ";
            }
            if ($participant->primary_email) {
                $str .= "Added Email. ";
            }
            if ($participant->notes) {
                $str .= "Added Note. ";
            }
            if (!$str) {
                $str = 'Voter became a Participant.';
            }
            addActionFromObject($participant, 'Added', $str, null);
        }
        
    }
    
}
