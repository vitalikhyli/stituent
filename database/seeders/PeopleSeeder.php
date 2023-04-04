<?php

namespace Database\Seeders;

use App\Models\CC\CCPrivateVoter;
use App\Models\CC\CCUser;
use App\Models\CC\CCVoter;
use App\Person;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valid_campaign_ids = Team::pluck('old_cc_id')
                    ->unique();

        if (env('APP_ENV') == 'local') {
            $valid_campaign_ids = [1];
        }

        //dd($valid_campaign_ids);

        Person::truncate();
        echo "Clearing current people\n";

        $cc_voters_builder = CCVoter::where('voters_campaignID', '>', 0)
                            ->whereIn('voters_campaignID', $valid_campaign_ids);

        echo 'About to add '.$cc_voters_builder->count()." People\n";

        $currcount = 0;
        $cc_voters_builder->chunk(10000, function ($cc_voters) use (&$currcount) {
            echo 'Processing '.$currcount." people\n";
            $currcount += 10000;

            foreach ($cc_voters as $cc_voter) {
                $person = Person::where('old_cc_id', $cc_voter->voterID)->first();
                if (! $person) {
                    $person = new Person;
                    $team = Team::where('old_cc_id', $cc_voter->voters_campaignID)->first();
                    if (! $team) {
                        echo 'Team not found! '.$cc_voter->voters_campaignID.' Code: '.$cc_voter->voter_code."\n";
                        continue;
                    }
                    $person->team_id = $team->id;

                    $person->voter_id = null;

                    $person->name_title = ! $cc_voter->name_title ? null : $cc_voter->name_title;
                    $person->first_name = ! $cc_voter->first_name ? null : $cc_voter->first_name;
                    $person->middle_name = ! $cc_voter->middle_name ? null : $cc_voter->middle_name;
                    $person->last_name = ! $cc_voter->last_name ? null : $cc_voter->last_name;

                    $person->address_number = ! $cc_voter->rstnum ? null : $cc_voter->rstnum;
                    $person->address_street = ! $cc_voter->rstname ? null : $cc_voter->rstname;
                    $person->address_apt = ! $cc_voter->rstapt ? null : $cc_voter->rstapt;
                    $person->address_city = ! $cc_voter->rcity ? null : $cc_voter->rcity;
                    $person->address_state = ! $cc_voter->rstate ? 'MA' : $cc_voter->rstate;
                    $person->address_zip = ! $cc_voter->rzip ? null : $cc_voter->rzip;

                    $person->gender = ! $cc_voter->sex ? null : $cc_voter->sex;

                    // ======================================================> Personal
                    $person->spouse_name = $cc_voter->spouse_name;
                    $phones = [];
                    if ($cc_voter->cell_phone) {
                        $phones[] = $cc_voter->cell_phone;
                    }
                    if ($cc_voter->home_phone) {
                        $phones[] = $cc_voter->home_phone;
                    }
                    if (count($phones) > 0) {
                        $person->primary_phone = $phones[0];
                        if (count($phones) > 1) {
                            $cc_voter->other_phones = $phones[1];
                        }
                    }
                    $person->deceased = $this->convertToBoolean($cc_voter->isdeceased);

                    if ($tempdate = $this->dateIsClean($cc_voter->deceased_date)) {
                        $person->deceased_date = $tempdate;
                    }
                    if (! is_numeric($cc_voter->party) && $cc_voter->party) {
                        $person->party = $cc_voter->party;
                    }
                    // ======================================================> Mailing Address
                    $mailing = [];
                    $mailing['address'] = $cc_voter->maddress_1;
                    $mailing['address2'] = $cc_voter->maddress_2;
                    $mailing['city'] = $cc_voter->mcity;
                    $mailing['state'] = $cc_voter->mstate;
                    $mailing['zip'] = $cc_voter->mzip;
                    $mailing['zip4'] = $cc_voter->mzip4;
                    $person->mailing_info = $mailing;

                    // ======================================================> Political districts

                    $person->governor_district = ! $cc_voter->gov_district ? null : $cc_voter->gov_district;
                    $person->congress_district = ! $cc_voter->congress_district ? null : $cc_voter->congress_district;
                    $person->senate_district = ! $cc_voter->senate_district ? null : $cc_voter->senate_district;
                    $person->house_district = ! $cc_voter->house_district ? null : $cc_voter->house_district;

                    $person->county_code = ! $cc_voter->county_code ? null : $cc_voter->county_code;
                    $person->city_code = ! $cc_voter->city_code ? null : $cc_voter->city_code;

                    $person->ward = $this->cleanWardAndPrecinct($cc_voter->ward_code);
                    $person->precinct = $this->cleanWardAndPrecinct($cc_voter->precinct_code);

                    // ======================================================> Business Info
                    $business_info = [];
                    $business_info['mcrc16'] = $cc_voter->mcrc16;
                    $business_info['occupation'] = $cc_voter->occupation;
                    $business_info['work_phone'] = $cc_voter->work_phone;
                    $business_info['work_phone_ext'] = $cc_voter->work_phone_ext;
                    $business_info['fax'] = $cc_voter->fax;
                    $business_info['name'] = $cc_voter->bname;
                    $business_info['address_1'] = $cc_voter->baddress_1;
                    $business_info['address_2'] = $cc_voter->baddress_2;
                    $business_info['city'] = $cc_voter->bcity;
                    $business_info['state'] = $cc_voter->bstate;
                    $business_info['zip'] = $cc_voter->bzip;
                    $business_info['zip4'] = $cc_voter->bzip4;
                    $business_info['fax'] = $cc_voter->bfax;
                    $business_info['web'] = $cc_voter->bweb;
                    $person->business_info = $business_info;

                    // ======================================================> Email
                    $emails = [];
                    if ($cc_voter->email) {
                        $emails[] = $cc_voter->email;
                    }
                    if ($cc_voter->email2) {
                        $emails[] = $cc_voter->email2;
                    }
                    if ($cc_voter->email3) {
                        $emails[] = $cc_voter->email3;
                    }
                    if (count($emails) > 0) {
                        $person->primary_email = $emails[0];
                        if (count($emails) > 1) {
                            $person->other_emails = array_slice($emails, 1);
                        }
                    }
                    if ($tempdate = $this->dateIsClean($cc_voter->create_date)) {
                        $person->created_at = $tempdate;
                    }

                    if ($tempdate = $this->dateIsClean($cc_voter->update_date)) {
                        $person->updated_at = $tempdate;
                    }
                    $person->old_cc_id = $cc_voter->voterID;
                    $person->old_voter_code = $cc_voter->voter_code;
                    $person->save();
                }
            }
        });

        $private_voters_builder = CCPrivateVoter::whereIn('campaignID', $valid_campaign_ids);

        //dd($private_voters);
        $pvcount = $private_voters_builder->count();
        echo "Starting to process $pvcount Private Voters\n";

        $currcount = 0;
        $private_voters_builder->chunk(10000, function ($private_voters) use (&$currcount) {
            echo "Starting chunk $currcount Private Voters.\n";

            $currcount += 10000;
            foreach ($private_voters as $pv) {
                $team = Team::where('old_cc_id', $pv->campaignID)->first();
                if (! $team) {
                    echo 'Team not found! '.$pv->campaignID."\n";
                    continue;
                }

                if (Str::startsWith($pv->voter_code, 'BG') || Str::startsWith($pv->voter_code, 'NE')) {
                    $person = findPersonOrImportVoter($pv->voter_code, $team->id);
                } else {
                    $person = findPersonOrImportVoter('MA_'.$pv->voter_code, $team->id);
                }
                if (! $person) {
                    echo $team->name.": Couldn't find person ".$pv->voter_code."\n";
                    $person = new Person;
                    $person->team_id = $team->id;
                    $person->address_state = $pv->voter_state;
                    $person->old_cc_id = $pv->voterID;
                    $person->old_voter_code = $pv->voter_code;
                }

                $person->master_email_list = $this->convertToBoolean($pv->mastermail);
                $person->massemail_neversend = $this->convertToBoolean($pv->noemail);

                $person->private = $pv->ssnumber;

                $other_emails = $person->other_emails;
                if ($pv->cms_voter_private_email1) {
                    if (! $person->primary_email) {
                        $person->primary_email = $pv->cms_voter_private_email1;
                    } else {
                        $other_emails[] = $pv->cms_voter_private_email1;
                    }
                }
                if ($pv->cms_voter_private_email2) {
                    $other_emails[] = $pv->cms_voter_private_email2;
                }
                $person->other_emails = $other_emails;

                $other_phones = $person->other_phones;
                if ($pv->cms_voter_private_home_phone) {
                    $other_phones[] = $pv->cms_voter_private_home_phone;
                }
                if ($pv->cms_voter_private_mobile_phone) {
                    $other_phones[] = $pv->cms_voter_private_mobile_phone;
                }
                $person->other_phones = $other_phones;
                $person->old_private = $pv->toArray();

                if ($tempdate = $this->dateIsClean($pv->create_date)) {
                    $person->created_at = $tempdate;
                }

                if ($tempdate = $this->dateIsClean($pv->update_date)) {
                    $person->updated_at = $tempdate;
                }

                $person->save();

                //dd($person);
            }
        });

        echo "Refreshing counts\n";
        foreach (Team::whereNotNull('db_slice')->get() as $team) {
            $voters_count = DB::table($team->db_slice)->count();
            $people_count = $team->people()->whereNull('voter_id')->count();
            $totals_count = $voters_count + $people_count;
            $team->constituents_count = $totals_count;
            $team->save();
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
