<?php

namespace Database\Seeders;

use App\CasePerson;
use App\Models\CC\CCCase;
use App\Models\CC\CCUser;
use App\Team;
use App\User;
use App\WorkCase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkCase::truncate();
        CasePerson::truncate();

        $valid_campaign_ids = Team::pluck('old_cc_id')
                    ->unique();
        //dd($valid_campaign_ids);
        $cc_cases = CCCase::whereIn('campaignID', $valid_campaign_ids)
                          ->where('priority', '>', 0)
                          ->get();
        $extra_cases = CCCase::whereIn('campaignID', $valid_campaign_ids)
                          ->where('priority', '=', 0)
                          ->where('short_desc', '')
                          ->get();
        $cc_cases = $cc_cases->merge($extra_cases)->sortBy('contact_issueID');

        $count = 0;
        foreach ($cc_cases as $cc_case) {
            $count++;

            $team = Team::where('old_cc_id', $cc_case->campaignID)->first();
            if (Str::startsWith($cc_case->voter_code, 'BG') || Str::startsWith($cc_case->voter_code, 'NE')) {
                $person = findPersonOrImportVoter($cc_case->voter_code, $team->id);
            } else {
                $person = findPersonOrImportVoter('MA_'.$cc_case->voter_code, $team->id);
            }

            if (! $person) {
                echo $team->name.": Couldn't find person ".$cc_case->voter_code."\n";
                continue;
            }

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

            if ($tempdate = $this->dateIsClean($cc_case->create_date)) {
                $case->date = $tempdate;
            }

            $case->subject = $cc_case->short_desc;
            $case->notes = $cc_case->issue;

            if ($tempdate = $this->dateIsClean($cc_case->create_date)) {
                $case->created_at = $tempdate;
            }
            if ($tempdate = $this->dateIsClean($cc_case->update_date)) {
                $case->updated_at = $tempdate;
            }

            $case->old_cc_id = $cc_case->contact_issueID;

            $case->save();

            $caseperson = new CasePerson;
            $caseperson->team_id = $team->id;
            $caseperson->case_id = $case->id;
            $caseperson->person_id = $person->id;
            if ($tempdate = $this->dateIsClean($cc_case->create_date)) {
                $caseperson->created_at = $tempdate;
            }
            if ($tempdate = $this->dateIsClean($cc_case->update_date)) {
                $caseperson->updated_at = $tempdate;
            }
            $caseperson->save();

            if ($count % 1000 == 0) {
                echo $count."\n";
            }
        }
    }

    public function dateIsClean($date)
    {
        if (! $date) {
            return false;
        }
        if (Str::startsWith($date, '0000-00-00')) {
            return false;
        }

        try {
            $carbondate = Carbon::parse($date);
        } catch (\Exception $e) {
            return false;
        }
        $datearr = explode('-', $date);
        $year = (int) $datearr[0];
        $month = (int) $datearr[1];
        $day = (int) $datearr[2];
        if ($day < 1) {
            $day = 1;
        }
        if ($month < 1) {
            $month = 1;
        }
        $carbondate = Carbon::parse("$year-$month-$day");
        if ($carbondate > Carbon::parse('1900-01-01')) {
            return "$year-$month-$day";
        }

        return false;
    }
}
