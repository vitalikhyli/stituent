<?php

namespace Database\Seeders;

use App\Contact;
use App\ContactPerson;
use App\Models\CC\CCCallLog;
use App\Models\CC\CCVoterContact;
use App\Models\CC\CCVoterNote;
use App\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "****************** STARTING CONTACTS ****************\n";
        Contact::truncate();

        $teams = Team::all();

        foreach ($teams as $team) {
            $cc_calls = CCCallLog::where('campaign_id', $team->old_cc_id)->with('ccUser')->get();
            echo 'Processing '.$cc_calls->count().' Call Logs for '.$team->name."\n";
            foreach ($cc_calls as $cc_call) {
                $contact = new Contact;
                $contact->team_id = $team->id;
                $user = $team->users()->where('username', $cc_call->ccUser->login)->first();
                if ($user) {
                    $contact->user_id = $user->id;
                } else {
                    $contact->user_id = 0;
                }
                $contact->subject = $cc_call->subject;
                $contact->notes = $cc_call->notes;
                $contact->private = $cc_call->private ? true : false;
                $contact->followup = $cc_call->todo ? true : false;
                $contact->call_log = true;

                $contact->date = $cc_call->created_at;
                if ($tempdate = dateIsClean($cc_call->created_at)) {
                    $contact->created_at = $tempdate;
                }
                if ($tempdate = dateIsClean($cc_call->updated_at)) {
                    $contact->updated_at = $tempdate;
                }
                $contact->save();
            }
        }

        foreach ($teams as $team) {
            $cc_notes = CCVoterNote::where('campaignID', $team->old_cc_id)->get();
            echo 'Processing '.$cc_notes->count().' Voter Notes for '.$team->name."\n";
            foreach ($cc_notes as $cc_note) {
                $contact = new Contact;
                $contact->team_id = $team->id;
                $user = $team->users()->where('username', $cc_note->create_login)->first();
                if ($user) {
                    $contact->user_id = $user->id;
                } else {
                    $contact->user_id = 0;
                }
                $contact->notes = strip_tags($cc_note->note);
                $contact->date = $cc_note->create_date;
                if ($tempdate = dateIsClean($cc_note->create_date)) {
                    $contact->created_at = $tempdate;
                }
                if ($tempdate = dateIsClean($cc_note->update_date)) {
                    $contact->updated_at = $tempdate;
                }
                $contact->save();

                if (Str::startsWith($cc_note->voter_code, 'BG') || Str::startsWith($cc_note->voter_code, 'NE')) {
                    $person = findPersonOrImportVoter($cc_note->voter_code, $team->id);
                } else {
                    $person = findPersonOrImportVoter('MA_'.$cc_note->voter_code, $team->id);
                }
                if (! $person) {
                    echo $team->name." Couldn't find ".$cc_note->voter_code."\n";
                    continue;
                }

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

        foreach ($teams as $team) {
            $cc_notes = CCVoterContact::where('campaignID', $team->old_cc_id)->get();
            echo 'Processing '.$cc_notes->count().' Voter Notes for '.$team->name."\n";
            foreach ($cc_notes as $cc_note) {
                $contact = new Contact;
                $contact->team_id = $team->id;
                $user = $team->users()->where('username', $cc_note->create_login)->first();
                if ($user) {
                    $contact->user_id = $user->id;
                } else {
                    $contact->user_id = 0;
                }
                $contact->notes = strip_tags($cc_note->note);
                $contact->date = $cc_note->create_date;
                if ($tempdate = dateIsClean($cc_note->create_date)) {
                    $contact->created_at = $tempdate;
                }
                if ($tempdate = dateIsClean($cc_note->update_date)) {
                    $contact->updated_at = $tempdate;
                }
                $contact->save();

                if (Str::startsWith($cc_note->voter_code, 'BG') || Str::startsWith($cc_note->voter_code, 'NE')) {
                    $person = findPersonOrImportVoter($cc_note->voter_code, $team->id);
                } else {
                    $person = findPersonOrImportVoter('MA_'.$cc_note->voter_code, $team->id);
                }
                if (! $person) {
                    echo $team->name." Couldn't find ".$cc_note->voter_code."\n";
                    continue;
                }

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
    }
}
