<?php

namespace App\Models\CC;

use App\Person;
use App\VoterMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CCVoter extends Model
{
    protected $primaryKey = 'voterID';
    protected $table = 'cms_voters';
    public $timestamps = false;
    protected $connection = 'cc_local';

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function elections()
    {
        return $this->hasMany(CCElection::class, 'voter_code', 'voter_code');
    }

    public function privateVoter()
    {
        return $this->hasOne(CCPrivateVoter::class, 'voterID', 'voterID');
    }

    public function createAndReturnPerson()
    {
        $person = new Person;

        $person->name_title = ! $this->name_title ? null : $this->name_title;
        $person->first_name = ! $this->first_name ? null : $this->first_name;
        $person->middle_name = ! $this->middle_name ? null : $this->middle_name;
        $person->last_name = ! $this->last_name ? null : $this->last_name;

        $person->address_number = ! $this->rstnum ? null : $this->rstnum;
        $person->address_street = ! $this->rstname ? null : $this->rstname;
        $person->address_apt = ! $this->rstapt ? null : $this->rstapt;
        $person->address_city = ! $this->rcity ? null : $this->rcity;
        $person->address_state = ! $this->rstate ? 'MA' : $this->rstate;
        $person->address_zip = ! $this->rzip ? null : $this->rzip;

        if (abs((int) $this->voters_lat) > 0) {
            $person->address_lat = ! $this->voters_lat ? null : $this->voters_lat;
        }
        if (abs((int) $this->voters_long) > 0) {
            $person->address_long = ! $this->voters_long ? null : $this->voters_long;
        }

        $person->gender = ! $this->sex ? null : $this->sex;

        // ======================================================> Personal
        $person->spouse_name = $this->spouse_name;
        $phones = [];
        if ($this->cell_phone) {
            $phones[] = $this->cell_phone;
        }
        if ($this->home_phone) {
            $phones[] = $this->home_phone;
        }
        if (count($phones) > 0) {
            $person->primary_phone = $phones[0];
            if (count($phones) > 1) {
                $this->other_phones = $phones[1];
            }
        }
        $person->deceased = $this->convertToBoolean($this->isdeceased);

        if ($tempdate = dateIsClean($this->deceased_date)) {
            $person->deceased_date = $tempdate;
        }
        if (! is_numeric($this->party) && $this->party) {
            $person->party = $this->party;
        }
        // ======================================================> Mailing Address
        $mailing = [];
        $mailing['address'] = $this->maddress_1;
        $mailing['address2'] = $this->maddress_2;
        $mailing['city'] = $this->mcity;
        $mailing['state'] = $this->mstate;
        $mailing['zip'] = $this->mzip;
        $mailing['zip4'] = $this->mzip4;
        $person->mailing_info = $mailing;

        // ======================================================> Political districts

        $person->governor_district = ! $this->gov_district ? null : $this->gov_district;
        $person->congress_district = ! $this->congress_district ? null : $this->congress_district;
        $person->senate_district = ! $this->senate_district ? null : $this->senate_district;
        $person->house_district = ! $this->house_district ? null : $this->house_district;

        $person->county_code = ! $this->county_code ? null : $this->county_code;
        $person->city_code = ! $this->city_code ? null : $this->city_code;

        $person->ward = $this->cleanWardAndPrecinct($this->ward_code);
        $person->precinct = $this->cleanWardAndPrecinct($this->precinct_code);

        // ======================================================> Business Info
        $business_info = [];
        $business_info['mcrc16'] = $this->mcrc16;
        $business_info['occupation'] = $this->occupation;
        $business_info['work_phone'] = $this->work_phone;
        $business_info['work_phone_ext'] = $this->work_phone_ext;
        $business_info['fax'] = $this->fax;
        $business_info['name'] = $this->bname;
        $business_info['address_1'] = $this->baddress_1;
        $business_info['address_2'] = $this->baddress_2;
        $business_info['city'] = $this->bcity;
        $business_info['state'] = $this->bstate;
        $business_info['zip'] = $this->bzip;
        $business_info['zip4'] = $this->bzip4;
        $business_info['fax'] = $this->bfax;
        $business_info['web'] = $this->bweb;
        $person->business_info = $business_info;

        // ======================================================> Email
        $emails = [];
        if ($this->email) {
            $emails[] = $this->email;
        }
        if ($this->email2) {
            $emails[] = $this->email2;
        }
        if ($this->email3) {
            $emails[] = $this->email3;
        }
        if (count($emails) > 0) {
            $person->primary_email = $emails[0];
            if (count($emails) > 1) {
                $person->other_emails = array_slice($emails, 1);
            }
        }
        if ($tempdate = dateIsClean($this->create_date)) {
            $person->created_at = $tempdate;
        }

        if ($tempdate = dateIsClean($this->update_date)) {
            $person->updated_at = $tempdate;
        }
        $person->old_cc_id = $this->voterID;
        $person->old_voter_code = $this->voter_code;

        return $person;
    }

    public function convertAddOrUpdate()
    {
        $firsttwo = substr($this->voter_code, 0, 2);
        if (! is_numeric($firsttwo)) {
            // These are added by users, not official voter codes
            // Handle them elsewhere
            return;
        }

        $voter = VoterMaster::find('MA_'.$this->voter_code);
        if (! $voter) {
            $voter = new VoterMaster;
            $voter->id = 'MA_'.$this->voter_code;
        }
        $voter->import_order = $this->voterID;

        // ======================================================> Name
        $voter->name_title = $this->name_title ?: null;
        $voter->first_name = $this->first_name ?: null;
        $voter->middle_name = $this->middle_name ?: null;
        $voter->last_name = $this->last_name ?: null;
        $voter->suffix_name = $this->suffix_name ?: null;

        // ======================================================> Address
        if ($this->rstpre != 'JR') {
            $voter->address_prefix = $this->rstpre ?: null;
        }
        $voter->address_number = $this->rstnum ?: null;
        $voter->address_fraction = $this->rstfrac ?: null;

        $street_name = $this->rstname;
        $street_type = '';
        $street_arr = explode(' ', strtoupper($this->rstname));
        foreach ($street_arr as $str) {
            if (isset($this->street_types[$str])) {
                $street_type = $str;
                break;
            }
        }
        $voter->address_street = trim($street_name) ?: null;
        $voter->address_street_type = $street_type ?: null;
        $voter->address_post = $this->rstpost ?: null;

        $voter->address_apt_type = $this->rstapttype ?: null;
        $voter->address_apt = $this->rstapt ?: null;
        $voter->address_city = $this->rcity ?: null;
        $voter->address_state = $this->rstate ?: null;

        $zip4 = '';
        $zip = substr($this->rzip, 0, 5);
        $zip = str_pad($zip, 5, '0', STR_PAD_LEFT);
        if (strlen($this->rzip) > 5) {
            $zip4 = substr($this->rzip, 5, 4);
        }
        $voter->address_zip = $zip ?: null;
        if ($zip4) {
            $voter->address_zip4 = $zip4;
        } else {
            $voter->address_zip4 = $this->rzip4 ?: null;
        }
        $voter->address_lat = $this->voters_lat ?: null;
        $voter->address_long = $this->voters_long ?: null;

        // ======================================================> Elections
        $elections = [];
        foreach ($this->elections as $election) {
            $elections[$election->code] = $election->voter_info;
        }
        ksort($elections);
        $voter->elections = $elections;

        // ======================================================> Demographic Data
        if ($this->sex == 'M' || $this->sex == 'F') {
            $voter->gender = $this->sex;
        }
        if (! is_numeric($this->party)) {
            $voter->party = $this->party;
        }
        if ($tempdate = $this->dateIsClean($this->dob)) {
            $voter->dob = $tempdate;
        }
        if ($tempdate = $this->dateIsClean($this->reg_date)) {
            $voter->registration_date = $tempdate;
        }
        if ($this->voter_status == 'A' || $this->voter_status == 'I') {
            $voter->voter_status = $this->voter_status;
        }

        if ($this->voters_ethnicity) {
            $voter->ethnicity = $this->voters_ethnicity;
        }
        $voter->head_household = $this->convertToBoolean($this->voters_head_household);

        // ======================================================> "Enriched Columns"
        // HANDLED IN SAVE FUNCTION FOR VOTER MODEL

        // ======================================================> Political districts
        $voter->state = 'MA';
        $voter->governor_district = $this->gov_district;
        $voter->congress_district = $this->congress_district;
        $voter->senate_district = $this->senate_district;
        $voter->house_district = $this->house_district;

        $voter->county_code = $this->county_code;
        $voter->city_code = $this->city_code;

        $voter->ward = $this->cleanWardAndPrecinct($this->ward_code);
        $voter->precinct = $this->cleanWardAndPrecinct($this->precinct_code);

        // ======================================================> Personal
        $voter->spouse_name = $this->spouse_name;
        $voter->cell_phone = $this->cell_phone;
        $voter->home_phone = $this->home_phone;
        $voter->deceased = $this->convertToBoolean($this->isdeceased);

        if ($tempdate = $this->dateIsClean($this->deceased_date)) {
            $voter->deceased_date = $tempdate;
        }

        // ======================================================> Mailing Address
        $mailing = [];
        $mailing['address'] = $this->maddress_1;
        $mailing['address2'] = $this->maddress_2;
        $mailing['city'] = $this->mcity;
        $mailing['state'] = $this->mstate;
        $mailing['zip'] = $this->mzip;
        $mailing['zip4'] = $this->mzip4;
        $voter->mailing_info = $mailing;

        // ======================================================> Other Emails
        $emails = [];
        if ($this->email) {
            $emails[] = $this->email;
        }
        if ($this->email2) {
            $emails[] = $this->email2;
        }
        if ($this->email3) {
            $emails[] = $this->email3;
        }
        $voter->emails = $emails;

        // ======================================================> Business Info
        $business_info = [];
        $business_info['mcrc16'] = $this->mcrc16;
        $business_info['occupation'] = $this->occupation;
        $business_info['work_phone'] = $this->work_phone;
        $business_info['work_phone_ext'] = $this->work_phone_ext;
        $business_info['fax'] = $this->fax;
        $business_info['name'] = $this->bname;
        $business_info['address_1'] = $this->baddress_1;
        $business_info['address_2'] = $this->baddress_2;
        $business_info['city'] = $this->bcity;
        $business_info['state'] = $this->bstate;
        $business_info['zip'] = $this->bzip;
        $business_info['zip4'] = $this->bzip4;
        $business_info['fax'] = $this->bfax;
        $business_info['web'] = $this->bweb;
        $voter->business_info = $business_info;

        // ======================================================> Alternate Districts
        $alternate_districts = [];
        $alternate_districts['alt_senate_district'] = $this->alt_senate_district;
        $alternate_districts['alt_house_district'] = $this->alt_house_district;
        $alternate_districts['alt_congress_district'] = $this->alt_congress_district;
        $alternate_districts['alt_gov_district'] = $this->alt_gov_district;
        $alternate_districts['custom_district'] = $this->custom_district;
        $voter->alternate_districts = $alternate_districts;

        // ======================================================> Admin
        $voter->origin_method = $this->voters_origin_method;

        if ($this->archive_date > '1900-00-00' && $this->archived == 'y') {
            $voter->archived_at = $this->archive_date;
            $voter->deleted_at = $this->archive_date;
        }

        if ($tempdate = $this->dateIsClean($this->create_date)) {
            $voter->created_at = $tempdate;
        }

        if ($tempdate = $this->dateIsClean($this->update_date)) {
            $voter->updated_at = $tempdate;
        }

        $voter->save();
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
