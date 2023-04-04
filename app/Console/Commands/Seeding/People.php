<?php

namespace App\Console\Commands\Seeding;

use App\Models\CC\CCPrivateVoter;
use App\Models\CC\CCUser;
use App\Models\CC\CCVoter;
use App\Models\CC\CCVoterArchive;
use App\Person;
use App\Team;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class People extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_people {--campaign=}';

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
        if (! $this->confirm('PEOPLE: Do you wish to continue?')) {
            return;
        }

        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting People with Campaign ID '.$this->option('campaign')."\n";
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::whereAppType('office')
                                      ->pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting People with '.count($valid_campaign_ids)." Campaigns\n";
        }

        $people_added = 0;
        $people_missing = 0;

        foreach ($valid_campaign_ids as $campaign_id) {
            $cc_voters_builder = CCVoter::where('voters_campaignID', '>', 0)
                                        ->where('voters_campaignID', $campaign_id);

            $team = Team::where('app_type', 'office')->where('old_cc_id', $campaign_id)->first();

            echo date('Y-m-d h:i:s').' '.$team->name.': About to add '.$cc_voters_builder->count()." People\n";

            if (!session('live')) {
                return;
            }

            $currcount = 0;

            $cc_voters_builder->chunk(1000, function ($cc_voters) use (&$currcount, &$people_added, &$people_missing, &$team) {
                echo date('Y-m-d h:i:s').' Processing '.$currcount." people\n";
                $currcount += 1000;

                foreach ($cc_voters as $cc_voter) {
                    $person = Person::where('team_id', $team->id)
                                    ->where('old_cc_id', $cc_voter->voterID)->first();
                    if (! $person) {
                        $person = $cc_voter->createAndReturnPerson();
                        $person->team_id = $team->id;
                        $person->save();
                        $people_added++;
                    }
                }
            });

            $private_voters_builder = CCPrivateVoter::whereIn('campaignID', $valid_campaign_ids);

            //dd($private_voters);
            $pvcount = $private_voters_builder->count();
            echo date('Y-m-d h:i:s')." Starting to process $pvcount Private Voters\n";

            $currcount = 0;
            $private_voters_builder->chunk(1000, function ($private_voters) use (&$currcount, &$people_added, &$people_missing, &$team) {
                echo date('Y-m-d h:i:s')." Starting chunk $currcount Private Voters.\n";

                $currcount += 1000;
                foreach ($private_voters as $pv) {
                    $team = Team::where('old_cc_id', $pv->campaignID)->first();
                    if (! $team) {
                        echo 'Team not found! '.$pv->campaignID."\n";
                        $people_missing++;
                        continue;
                    }
                    $person = Person::where('team_id', $team->id)
                                    ->where('old_voter_code', $pv->voter_code)->first();
                    if (! $person) {
                        if (Str::startsWith($pv->voter_code, 'BG') || Str::startsWith($pv->voter_code, 'NE')) {
                            $person = findPersonOrImportVoter($pv->voter_code, $team->id);
                        } else {
                            $person = findPersonOrImportVoter('MA_'.$pv->voter_code, $team->id);
                            if (! $person) {

                                // Double check remote voters table
                                $ccvoter = CCVoter::where('voter_code', $pv->voter_code)->first();
                                if ($ccvoter) {
                                    $person = $ccvoter->createAndReturnPerson();
                                    $person->team_id = $team->id;
                                } else {
                                    // Check archived table
                                    $archived = CCVoterArchive::where('voter_code', $pv->voter_code)->first();
                                    if ($archived) {
                                        $person = $archived->createAndReturnPerson();
                                        $person->team_id = $team->id;
                                    }
                                }
                            }
                        }
                    }
                    if (! $person) {
                        echo date('Y-m-d h:i:s').' '.$team->name.": Couldn't find person ".$pv->voter_code."\n";
                        $person = new Person;
                        $person->team_id = $team->id;
                        $person->address_state = $pv->voter_state;
                        $person->old_cc_id = $pv->voterID;
                        $person->old_voter_code = $pv->voter_code;
                        $people_missing++;
                    }

                    $person->master_email_list = $this->convertToBoolean($pv->mastermail);
                    $person->massemail_neversend = $this->convertToBoolean($pv->noemail);

                    $person->private = $pv->ssnumber;

                    // $other_emails = $person->other_emails;
                    // if ($pv->cms_voter_private_email1) {
                    //     if (!$person->primary_email) {
                    //             $person->primary_email = $pv->cms_voter_private_email1;
                    //     } else {
                    //       $other_emails[] = $pv->cms_voter_private_email1;
                    //     }
                    // }
                    // if ($pv->cms_voter_private_email2) {
                    //     $other_emails[] = $pv->cms_voter_private_email2;
                    // }
                    // $person->other_emails = $other_emails;

                    // $other_phones = $person->other_phones;
                    // if ($pv->cms_voter_private_home_phone) {
                    //     $other_phones[] = $pv->cms_voter_private_home_phone;
                    // }
                    // if ($pv->cms_voter_private_mobile_phone) {
                    //     $other_phones[] = $pv->cms_voter_private_mobile_phone;
                    // }
                    // $person->other_phones = $other_phones;

                    //ADDING NOTES ELEMENT TO OTHER_EMAILS
                    $other_emails = [];

                    $other_emails[] = $person->other_emails; // THIS FIXES CONTACTINFO PROBLEM?

                    if ($pv->cms_voter_private_email1) {
                        if (! $person->primary_email) {
                            $person->primary_email = $pv->cms_voter_private_email1;
                        } else {
                            $other_emails[] = [$pv->cms_voter_private_email1, null];
                        }
                    }

                    if ($pv->cms_voter_private_email2) {
                        $other_emails[] = [$pv->cms_voter_private_email2, null];
                    }

                    $person->other_emails = ($other_emails) ? $other_emails : null;

                    //ADDING NOTES ELEMENT TO OTHER_PHONES
                    $other_phones = [];

                    $other_phones[] = $person->other_phones; // THIS FIXES CONTACTINFO PROBLEM?

                    if ($pv->cms_voter_private_home_phone) {
                        $other_phones[] = [$pv->cms_voter_private_home_phone, 'home'];
                    }

                    if ($pv->cms_voter_private_mobile_phone) {
                        $other_phones[] = [$pv->cms_voter_private_mobile_phone, 'mobile'];
                    }

                    $person->other_phones = ($other_phones) ? $other_phones : null;

                    $person->old_private = $pv->toArray();

                    if ($tempdate = dateIsClean($pv->create_date)) {
                        $person->created_at = $tempdate;
                    }

                    if ($tempdate = dateIsClean($pv->update_date)) {
                        $person->updated_at = $tempdate;
                    }

                    $person->save();
                    $people_added++;

                    //dd($person);
                }
            });
        }

        echo date('Y-m-d h:i:s')." Added $people_added people, missing $people_missing.\n";
    }

    public function convertToBoolean($val)
    {
        $val = trim($val);
        if ($val == 'y') {
            return true;
        }
        if ($val == 'Y') {
            return true;
        }
        if ($val == 1) {
            return true;
        }

        return false;
    }

    public function cleanWardAndPrecinct($val)
    {
        // have one or two entries in the live db
        $ignore = ['\N', 'I', '330', '163', 'war', '113', 'W  ', '08/', '3 G', '288', '151', '01/', '09/', '65', '289', '67', '164', '07/', '149', '171', '181', '189', 'pre', 'PO', 'C/O', '201', 'P O', '174'];

        if (in_array($val, $ignore)) {
            return null;
        }
        $val = ltrim($val, '0');
        $val = strtoupper($val);
        if (! $val) {
            $val = null;
        }

        return $val;
    }
}
