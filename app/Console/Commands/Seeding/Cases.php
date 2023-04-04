<?php

namespace App\Console\Commands\Seeding;

use App\CasePerson;
use App\Models\CC\CCCase;
use App\Models\CC\CCUser;
use App\Models\CC\CCVoter;
use App\Models\CC\CCVoterArchive;
use App\Person;
use App\Team;
use App\User;
use App\WorkCase;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Cases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_cases {--campaign=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //if (env('LOCAL_MACHINE') != 'Slothe')             return;
        if (! $this->confirm('CASES: Do you wish to continue?')) {
            return;
        }

        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting Cases with Campaign ID '.$this->option('campaign')."\n";
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::whereAppType('office')
                                      ->pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting Cases with '.count($valid_campaign_ids)." Campaigns\n";
        }

        foreach ($valid_campaign_ids as $campaign_id) {
            //dd($valid_campaign_ids);
            $team = Team::whereAppType('office')->where('old_cc_id', $campaign_id)->first();

            $cc_cases = CCCase::where('campaignID', $campaign_id)
                              ->where('priority', '>', 0)
                              ->with('ccVoter')
                              ->get();
            $extra_cases = CCCase::where('campaignID', $campaign_id)
                              ->where('priority', '=', 0)
                              ->where('short_desc', '')
                              ->with('ccVoter')
                              ->get();
            $cc_cases = $cc_cases->merge($extra_cases)->sortBy('contact_issueID');

            echo date('Y-m-d h:i:s').' '.$team->name.': About to add '.$cc_cases->count()." Cases\n";
            if (!session('live')) {
                return;
            }

            $count = 0;
            foreach ($cc_cases as $cc_case) {
                $count++;

                $person = Person::where('team_id', $team->id)
                                ->where('old_cc_id', $cc_case->voterID)->first();
                if (! $person) {
                    //dd($cc_assignment);
                    $ccvoter = $cc_case->ccVoter;
                    if (! $ccvoter) {
                        $ccvoter = CCVoter::where('voter_code', $cc_case->voter_code)->first();
                    }
                    if (! $ccvoter) {
                        $ccvoter = CCVoterArchive::find($cc_case->voterID);
                    }

                    if (! $ccvoter) {
                        echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_case->voterID."\n";

                        continue;
                    }
                    if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                        $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                    } else {
                        $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                    }
                }
                if (! $person) {
                    // Double check remote voters table

                    $person = $ccvoter->createAndReturnPerson();
                }
                if (! $person) {
                    $archived = CCVoterArchive::where('voter_code', $ccvoter->voter_code)->first();
                    if ($archived) {
                        $person = $archived->createAndReturnPerson();
                    }
                }

                if (! $person) {
                    echo date('Y-m-d h:i:s').' '.$team->name.": Couldn't find person ".$cc_case->voter_code."\n";
                    continue;
                }

                $person->team_id = $team->id;
                if (! $person->updated_at || $person->updated_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_case->update_date)) {
                        $person->updated_at = $tempdate;
                    }
                }
                if (! $person->created_at || $person->created_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_case->create_date)) {
                        $person->created_at = $tempdate;
                    }
                }
                $person->save();

                $case = WorkCase::where('team_id', $team->id)
                                ->where('old_cc_id', $cc_case->contact_issueID)
                                ->first();

                if (! $case) {
                    $case = new WorkCase;

                    $case->team_id = $team->id;
                    $user = User::where('current_team_id', $team->id)
                                ->where('username', '=', $cc_case->create_login)
                                ->first();
                    if (! $user) {
                        $user = User::where('current_team_id', $team->id)->first();
                        if (! $user) {
                            echo $cc_case->create_login.' not found, campaign ID '.$cc_case->campaignID."\n";
                            continue;
                        }
                    }
                    // Priority
                    $case->user_id = $user->id;
                    if ($cc_case->priority <= 1) {
                        $case->priority = 'Low';
                    }
                    if ($cc_case->priority == 2) {
                        $case->priority = 'Medium';
                    }
                    if ($cc_case->priority == 3) {
                        $case->priority = 'High';
                    }

                    // Status
                    if ($cc_case->issue_status == 'O') {
                        $case->status = 'open';
                    }
                    if ($cc_case->issue_status == 'C') {
                        $case->status = 'resolved';
                    }
                    if ($cc_case->issue_status == 'H') {
                        $case->status = 'held';
                    }

                    if ($tempdate = dateIsClean($cc_case->create_date)) {
                        $case->date = $tempdate;
                    }

                    $case->subject = $cc_case->short_desc;
                    $case->notes = $cc_case->issue;

                    if ($tempdate = dateIsClean($cc_case->create_date)) {
                        $case->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_case->update_date)) {
                        $case->updated_at = $tempdate;
                    }

                    $case->closing_remarks = $cc_case->closing_remarks;

                    $case->old_cc_id = $cc_case->contact_issueID;

                    $case->save();
                }

                $caseperson = CasePerson::where('person_id', $person->id)
                                        ->where('case_id', $case->id)
                                        ->where('team_id', $team->id)
                                        ->first();
                if (! $caseperson) {
                    $caseperson = new CasePerson;
                    $caseperson->team_id = $team->id;
                    $caseperson->case_id = $case->id;
                    $caseperson->person_id = $person->id;
                    if ($tempdate = dateIsClean($cc_case->create_date)) {
                        $caseperson->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_case->update_date)) {
                        $caseperson->updated_at = $tempdate;
                    }
                    $caseperson->save();
                }

                if ($count % 1000 == 0) {
                    echo date('Y-m-d h:i:s')." $count done.\n";
                }
            }
        }
    }
}
