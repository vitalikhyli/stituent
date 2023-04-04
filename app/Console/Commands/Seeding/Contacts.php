<?php

namespace App\Console\Commands\Seeding;

use App\Contact;
use App\ContactPerson;
use App\Models\CC\CCCallLog;
use App\Models\CC\CCUser;
use App\Models\CC\CCVoterArchive;
use App\Models\CC\CCVoterContact;
use App\Models\CC\CCVoterNote;
use App\Person;
use App\Team;
use App\WorkCase;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Contacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_contacts {--campaign=} {--login=}';

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
        if (! $this->confirm('CONTACTS: Do you wish to continue?')) {
            return;
        }

        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting Contacts with Campaign ID '.$this->option('campaign')."\n";
        } elseif ($this->option('login')) {
            $login = $this->option('login');
            $cc_user = CCUser::where('login', $login)->first();
            $valid_campaign_ids = [$cc_user->campaignID];
            dd();
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::whereAppType('office')
                                      ->pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting Contacts with '.count($valid_campaign_ids)." Campaigns\n";
            dd();
        }

        foreach ($valid_campaign_ids as $campaign_id) {
            $team = Team::whereAppType('office')->where('old_cc_id', $campaign_id)->first();

            $cc_calls = CCCallLog::where('campaign_id', $team->old_cc_id)->with('ccUser')->get();
            echo 'Processing '.$cc_calls->count().' Call Logs for '.$team->name."\n";
            if (!session('live')) {
                return;
            }
            foreach ($cc_calls as $cc_call) {
                $contact = Contact::where('team_id', $team->id)
                                  ->where('source', 'call_log')
                                  ->where('subject', $cc_call->subject)
                                  ->where('created_at', $cc_call->created_at)
                                  ->first();
                if (! $contact) {
                    $contact = new Contact;
                    $contact->team_id = $team->id;
                    $user = $team->users()->where('username', $cc_call->ccUser->login)->first();
                    if ($user) {
                        $contact->user_id = $user->id;
                    } else {
                        $newuser = createNewUserOnTeam($team, $cc_call->ccUser->login);
                        $contact->user_id = $newuser->id;
                    }
                    $contact->subject = $cc_call->subject;
                    $contact->notes = $cc_call->notes;
                    $contact->private = $cc_call->private ? true : false;
                    $contact->followup = $cc_call->todo ? true : false;

                    $contact->date = $cc_call->created_at;

                    $contact->created_at = $cc_call->created_at;
                    $contact->updated_at = $cc_call->updated_at;
                    $contact->source = 'call_log';

                    $contact->save();
                }
            }

            $cc_notes = CCVoterNote::where('campaignID', $team->old_cc_id)->get();
            echo 'Processing '.$cc_notes->count().' Voter Notes for '.$team->name."\n";
            foreach ($cc_notes as $cc_note) {
                $contact = Contact::where('team_id', $team->id)
                                  ->where('source', 'voter_note')
                                  ->where('old_cc_id', $cc_note->noteID)
                                  ->first();
                if (! $contact) {
                    $contact = new Contact;
                    $contact->team_id = $team->id;
                    $user = $team->users()->where('username', $cc_note->create_login)->first();
                    if ($user) {
                        $contact->user_id = $user->id;
                    } else {
                        $newuser = createNewUserOnTeam($team, $cc_note->create_login);
                        $contact->user_id = $newuser->id;
                    }
                    $contact->notes = strip_tags($cc_note->note);
                    $contact->date = $cc_note->create_date;
                    if ($tempdate = dateIsClean($cc_note->create_date)) {
                        $contact->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_note->update_date)) {
                        $contact->updated_at = $tempdate;
                    }
                    $contact->old_cc_id = $cc_note->noteID;
                    $contact->source = 'voter_note';
                    $contact->save();
                }
                $ccvoter = $cc_note->ccVoter;
                $person = Person::where('team_id', $team->id)
                                ->where('old_cc_id', $cc_note->voterID)->first();
                if (! $person) {
                    //dd($cc_assignment);

                    if (! $ccvoter) {
                        $ccvoter = CCVoterArchive::find($cc_note->voterID);
                    }
                    if (! $ccvoter) {
                        echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_note->voterID."\n";

                        continue;
                    }
                    if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                        $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                    } else {
                        $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                    }
                }
                if (! $ccvoter && ! $person) {
                    echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_contact->voterID." or person\n";
                    continue;
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
                    echo date('Y-m-d h:i:s').' '.$team->name.": Couldn't find person ".$cc_note->voter_code."\n";
                    continue;
                }

                $person->team_id = $team->id;

                if (! $person->updated_at || $person->updated_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_note->update_date)) {
                        $person->updated_at = $tempdate;
                    }
                }

                if (! $person->created_at || $person->created_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_note->create_date)) {
                        $person->created_at = $tempdate;
                    }
                }

                $person->save();

                $contact_person = ContactPerson::where('team_id', $team->id)
                                               ->where('person_id', $person->id)
                                               ->where('contact_id', $contact->id)
                                               ->first();
                if (! $contact_person) {
                    $contact_person = new ContactPerson;
                    $contact_person->team_id = $team->id;
                    $contact_person->person_id = $person->id;
                    $contact_person->contact_id = $contact->id;
                    if ($tempdate = dateIsClean($cc_note->create_date)) {
                        $contact_person->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_note->update_date)) {
                        $contact_person->updated_at = $tempdate;
                    }
                    $contact_person->save();
                }
            }

            $cc_contacts = CCVoterContact::where('campaignID', $team->old_cc_id)->with('ccVoter')->get();
            echo 'Processing '.$cc_contacts->count().' Voter Contacts for '.$team->name."\n";
            $count = 0;
            foreach ($cc_contacts as $cc_contact) {
                $count++;
                $contact = Contact::where('team_id', $team->id)
                                  ->where('source', 'case_contact')
                                  ->where('old_cc_id', $cc_contact->contactID)
                                  ->first();
                if (! $contact) {
                    $contact = new Contact;
                    $contact->team_id = $team->id;
                    $user = $team->users()->where('username', $cc_contact->create_login)->first();
                    if ($user) {
                        $contact->user_id = $user->id;
                    } else {
                        $newuser = createNewUserOnTeam($team, $cc_contact->create_login);
                        $contact->user_id = $newuser->id;
                    }
                    if ($case = WorkCase::where('old_cc_id', $cc_contact->contact_issueID)->first()) {
                        $contact->case_id = $case->id;
                    }
                    $contact->notes = strip_tags($cc_contact->note);
                    $contact->date = $cc_contact->create_date;
                    if ($tempdate = dateIsClean($cc_contact->create_date)) {
                        $contact->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_contact->update_date)) {
                        $contact->updated_at = $tempdate;
                    }
                    $contact->old_cc_id = $cc_contact->contactID;
                    $contact->source = 'case_contact';
                    $contact->save();
                }
                $ccvoter = $cc_contact->ccVoter;
                $person = Person::where('team_id', $team->id)
                                ->where('old_cc_id', $cc_contact->voterID)->first();
                if (! $person) {
                    //dd($cc_assignment);
                    if (! $ccvoter) {
                        $ccvoter = CCVoterArchive::find($cc_contact->voterID);
                    }
                    if (! $ccvoter) {
                        echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_contact->voterID."\n";

                        continue;
                    }
                    if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                        $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                    } else {
                        $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                    }
                }
                if (! $ccvoter && ! $person) {
                    echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_contact->voterID." or person\n";
                    continue;
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
                    echo date('Y-m-d h:i:s').' '.$team->name.": Couldn't find person ".$cc_contact->voter_code."\n";
                    continue;
                }

                $person->team_id = $team->id;

                if (! $person->updated_at || $person->updated_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_contact->update_date)) {
                        $person->updated_at = $tempdate;
                    }
                }

                if (! $person->created_at || $person->created_at > Carbon::yesterday()) {
                    if ($tempdate = dateIsClean($cc_contact->create_date)) {
                        $person->created_at = $tempdate;
                    }
                }

                $person->save();

                $contact_person = ContactPerson::where('team_id', $team->id)
                                               ->where('person_id', $person->id)
                                               ->where('contact_id', $contact->id)
                                               ->first();
                if (! $contact_person) {
                    $contact_person = new ContactPerson;
                    $contact_person->team_id = $team->id;
                    $contact_person->person_id = $person->id;
                    $contact_person->contact_id = $contact->id;
                    if ($tempdate = dateIsClean($cc_contact->create_date)) {
                        $contact_person->created_at = $tempdate;
                    }
                    if ($tempdate = dateIsClean($cc_contact->update_date)) {
                        $contact_person->updated_at = $tempdate;
                    }
                    $contact_person->save();
                }
                if ($count % 1000 == 0) {
                    echo date('Y-m-d h:i:s')." $count done.\n";
                }
            }
        }
    }
}
