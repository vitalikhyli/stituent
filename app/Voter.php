<?php

namespace App;

use App\District;
use App\Municipality;
use App\CampaignList;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\SharedCasesTrait;
use App\SharedCase;

use DateTimeInterface;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;

use DB;
use App\Traits\ModelUseIndex;


class Voter extends Model
{
    use SoftDeletes;
    use SharedCasesTrait;
    use SpatialTrait;
    use ModelUseIndex;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $dates = ['created_at', 'updated_at', 'dob', 'registration_date', 'processed_elections_at', 'archived_at'];

    protected $casts = [
        'mailing_info' 			=> 'array',
        'emails'       			=> 'array',
        'business_info' 		=> 'array',
        'alternate_districts'   => 'array',
        'elections' 			=> 'array',
        'original_import'       => 'array', //History of election participation
    ];

    protected $spatialFields = [
        'location'
    ];

    protected $appends = ['voter_id'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    ////////////////////////////////////////////////////////////////////////////////////

    public function replaceWithParticipantFields($fields)
    {
        if ($participant = $this->linkedParticipantInTeam(Auth::user()->team)) {
            
            // Change fetched model but do not save it:

            foreach($fields as $field) {
                $this->$field  = $participant->$field;
            }
            
        }

        return $this;
    }
    public function getAvatarAttribute($avatar)
    {
        // Handled in person -- Voter file has no avatars
        return '/images/avatar.png';
    }
    public function linkedParticipantInTeam($team)
    {
        return Participant::where('team_id', $team->id)
                          ->where('voter_id', $this->id)
                          ->first();
    }
    public function scopeBornOnDate($query, $date)
    {
        return $query->whereRaw("DATE_FORMAT(dob, '%m-%d') 
                                 = DATE_FORMAT('".$date->format('m-d')."', '%m-%d')");
    }
    public function getNextBirthdayAttribute()
    {
        if (!$this->dob) {
            return null;
        }
        $date = Carbon::parse($this->dob->format('Y-m-d'));
        $date->year(Carbon::now()->year);

        // diff from 31 may to now
        // its negative than add one year, otherwise use the current
        if (Carbon::now()->diffInDays($date, false) >= 0) {
            return $date;
        }

        return $date->addYear();
    }
    public function getPreviousBirthdayAttribute()
    {
        if (!$this->dob) {
            return null;
        }
        $date = $this->dob;
        $date->year(Carbon::now()->year);

        // diff from 31 may to now
        // its negative than add one year, otherwise use the current
        if (Carbon::now()->diffInDays($date, false) < 0) {
            return $date;
        }

        return $date->subYear();
    }

    public function cohabitators()
    {
        if (!$this->household_id) {
            return collect([]);
        }

        $cohabitors = Voter::where('household_id', $this->household_id)
                           ->where('id', '<>', $this->id)
                           ->get();

        return $cohabitors;
    }

    public function listsTheyBelongTo()
    {
        $lists = CampaignList::thisTeam()
                             ->whereNotNull('cached_voters')
                             ->where('cached_voters', 'like', '%'.$this->id.'%')
                             ->get();

        foreach(CampaignList::thisTeam()->whereNull('cached_voters')->get() as $uncached_list) {
            if (in_array($this->id, $uncached_list->getVoterIds())) {
                $lists[] = $uncached_list;
            }
        }

        return $lists;
    }

    public function cfPlus()
    {
        $cfplus = CFPlus::firstWhere('voter_id', $this->id);
        return $cfplus;
    }

    public function getCfPlusDataAttribute()
    {
        //dd("Laz");
        if ($cf_plus = $this->cfPlus()) {
            
            $str = "";
            if ($cf_plus->home_phone) {
                $str .= "Home: ".$cf_plus->home_phone."<br>";
            }
            if ($cf_plus->cell_phone) {
                $str .= "Cell: ".$cf_plus->cell_phone."<br>";
            }
            if ($cf_plus->ethnic_description) {
                $str .= "Ethnicity: ".$cf_plus->ethnic_description."<br>";
            }
            return $str;
        }
    }
    public function getPrimaryPhoneAttribute()
    {
        return $this->home_phone;
    }

    public function getPhonesAttribute()
    {
        $phones = [];
        if (!is_numeric($this->id) && $this->home_phone) {
            $phones['voter_file'] = $this->home_phone;
        }
        if ($this->primary_phone) {
            $phones['primary'] = $this->primary_phone;
        }
        if ($this->home_phone) {
            $phones['home'] = $this->home_phone;
        }
        if ($this->cell_phone) {
            $phones['cell'] = $this->cell_phone;
        }
        if ($this->other_phones) {
            // if (isset($this->other_phones[0][0])) {
            //     $phones['other'] = $this->other_phones[0][0];
            // }
            foreach ($this->other_phones as $i => $other) {
                $phones['other '.$other[1]] = $other[0];        // Puts name/type into category
            }
        }
        if ($this->work_phone) {
            $phones['work'] =  $this->work_phone;
        }
        if ($cf_plus = $this->cfPlus()) {
            if ($cf_plus->home_phone) {
                $phones['cf_plus_home'] = $cf_plus->home_phone;
            }
            if ($cf_plus->cell_phone) {
                $phones['cf_plus_cell'] = $cf_plus->cell_phone;
            }
        }
        return $phones;
    }

    public function tagWith($tag_id)
    {
        $participant = findParticipantOrImportVoter($this->id, Auth::user()->team_id);
        if (!$participant) return;

        $tag = Tag::find($tag_id);
        if (!$tag) return;

        $pivot = ParticipantTag::where('participant_id', $participant->id)
                               ->where('tag_id', $tag->id)
                               ->first();
        if (!$pivot) {
            $pt = new ParticipantTag;
            $pt->tag_id         = $tag->id;
            $pt->team_id        = Auth::user()->team_id;
            $pt->user_id        = Auth::user()->id;
            $pt->voter_id       = $participant->voter_id;
            $pt->participant_id = $participant->id;
            $pt->save();

            return 1;
        }
    }

    public function addEmail($new)
    {
        $participant = findParticipantOrImportVoter($this->id, Auth::user()->team_id);
        
        if (!$participant) return;
        if ($participant->primary_email == $new) return;

        if ($participant->primary_email && $participant->primary_email != $new) {
            $other_emails = $participant->other_emails;
            $other_emails[] = [$participant->primary_email, null];
            $participant->other_emails = $other_emails;
        }

        $participant->primary_email = $new;
        $participant->save();

        return 1;
    }

    public function addPhone($new)
    {
        $participant = findParticipantOrImportVoter($this->id, Auth::user()->team_id);
        
        if (!$participant) return;
        if ($participant->primary_phone == $new) return;

        if ($participant->primary_phone && $participant->primary_phone != $new) {
            $other_phones = $participant->other_phones;
            $other_phones[] = [$participant->primary_phone, null];
            $participant->other_phones = $other_phones;
        }
        
        $participant->primary_phone = $new;
        $participant->save();

        return 1;
    }

    public function sharedCases()
    {
        $shared_cases = $this->getSharedCases($this->id);
        return SharedCase::whereIn('id', $shared_cases->pluck('id'));
    }

    public function removeTag($tag_id)
    {
        $participant = Participant::thisTeam()->where('voter_id', $this->id)->first();
        if (!$participant) return;

        $tag = Tag::find($tag_id);
        if (!$tag) return;

        $pivot = ParticipantTag::where('participant_id', $participant->id)
                               ->where('tag_id', $tag->id)
                               ->first();
        if ($pivot) {
            $pivot->delete();

            return 1;
        }
        
    }
    public function getVoterAttribute()
    {
        if ($this->voter_id) {
            return Voter::find($this->voter_id);
        }
    }

    public function contacts()
    {
        return collect([]);
    }

    public function cases()
    {
        return collect([]);
    }

    public function groups()
    {
        return collect([]);
    }

    public function bulkEmails()
    {
        return collect([]);
    }

    public function calculateDistrictCode($district_type)
    {
        $dq = \App\VoterMaster::select($district_type)
                         ->where('city_code', $this->city_code);

        if ($this->ward > 0) {
            $dq->where('ward', $this->ward);
        }

        $district = $dq->where('precinct', $this->precinct)
                       ->whereNotNull($district_type)
                       ->where($district_type, '!=', 0)
                       ->first();
        //dd($district, $this);

        return (! $district) ? null : $district->$district_type;
    }

    ////////////////////////////////////////////////////////////////////////////////////

    public function scopeCloseTo($query, $latitude, $longitude)
    {
        return $query->where('address_lat', '<', $latitude + .001)
                     ->where('address_lat', '>', $latitude - .001)
                     ->where('address_long', '>', $longitude + .001)
                     ->where('address_long', '<', $longitude - .001);

        // Use like:
        // $nearby_households = Voting::closeTo($household->address_lat, $household->address_long)->get();

        // Other version (was in VotingHousehold)

        // return $query->whereNotNull('address_lat')
        //              ->whereRaw("
        //                    (ST_Distance_Sphere(
        //                         point(address_long, address_lat),
        //                         point(?, ?)
        //                     ) * .000621371192) < .001
        //                 ", [
        //                     $longitude,
        //                     $latitude,
        //                 ]);
    }

    public function participant()
    {
        //if (Auth::user()->team->id == 1) dd('!!!');
        return $this->belongsTo(Participant::class, 'voter_id')
                    ->where('team_id', Auth::user()->team->id);
    }

    public function hasTag($tagid)
    {
        return ParticipantTag::where('tag_id', $tagid)
                             ->where('voter_id', $this->id)
                             ->exists();
    }

    public function profile()
    {
        return $this->hasOne(ElectionProfile::class, 'voter_id', 'id');
    }

    public function range()
    {
        return $this->hasOne(ElectionRange::class, 'voter_id', 'id');
    }

    public function processing()
    {
        return $this->hasOne(VoterProcessing::class, 'voter_id', 'id');
    }

    public function support($campaign)
    {
        return null;
    }

    public function getSupportAttribute()
    {
        $participant = getParticipant($this);
        if (! $participant) {
            return null;
        }

        return $participant->support;
    }

    public function getPhoneAttribute()
    {
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            if ($participant->phone) {
                return $participant->phone;
            }
        }

        return $this->home_phone;
    }

    public function getEmailAttribute()
    {
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            if ($participant->email) {
                return $participant->email;
            }
        }

        return $this->emails_string;
    }
    public function getEmailsAttribute()
    {
        $emails = [];
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            if ($participant->emails) {
                return $participant->emails;
            }
        }
        return $emails;
    }

    public function getPhoneListAttribute()
    {
        $str = '';
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            if ($participant->phone) {
                $str .= $participant->phone.' ';
            }
        }
        if ($this->home_phone) {
            $str .= $this->home_phone.' ';
        }
        if ($this->cell_phone) {
            $str .= $this->cell_phone.' ';
        }

        return $str;
    }

    public function getReadablePhoneAttribute()
    {
        $phone = $this->phone;
        if (strlen($phone) == 10) {
            return '('.(substr($phone, 0, 3)).') '.substr($phone, 3, 3).'-'.substr($phone, 6, 4);
        }

        return $phone;
    }

    public function getActivityIconsAttribute()
    {
        // real ones are in person model
        $icons = [];
        return $icons;
    }

    public function getJustStreetNumTownAttribute()
    {
        $street_num_town = null;
        if ($this->address_number) {
            $street_num_town .= $this->address_number.' ';
        }
        if ($this->address_street) {
            $street_num_town .= $this->address_street.' ';
        }
        $street .= $this->address_city;
        return trim($street_num_town);
    }

    public function getTownAddressAttribute()
    {
        return $this->address_line_street.", ".$this->address_city;
    }

    public function getAddressLineStreetAttribute()
    {
        $street = null;
        if ($this->address_prefix) {
            $street .= $this->address_prefix.' ';
        }
        if ($this->address_number) {
            $street .= $this->address_number.' ';
        }
        if ($this->address_fraction) {
            $street .= $this->address_fraction.' ';
        }
        if ($this->address_street) {
            $street .= $this->address_street.' ';
        }
        if ($this->address_apt) {
            $address_apt = $this->address_apt;
            if (!Str::contains($address_apt, '#') || !Str::contains($address_apt, 'apt')) {
                $address_apt = '#'.$address_apt;
            }
            $street .= $address_apt.' ';
        }

        return trim($street);
    }

    public function actions()
    {
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            return $participant->actions();
        }
        return $this->hasMany(Action::class)->where('team_id', Auth::user()->team_id);

    }
    public function tags()
    {
        if (isParticipant($this)) {
            $participant = getParticipant($this);
            return $participant->tags();
        }
        //dd("Laz");
        return null;

    }
    public function getCampaignNotesAttribute()
    {
        $cps = CampaignParticipant::where('voter_id', $this->id)
                                  ->where('team_id', Auth::user()->team_id)
                                  ->get();
        return $cps->implode('notes', ', ');
    }

    public function getAddressCityZipAttribute()
    {
        $cz = null;
        if ($this->address_city) {
            $cz .= $this->address_city.', ';
        }
        if ($this->address_state) {
            $cz .= $this->address_state.' ';
        }
        if ($this->address_zip) {
            $cz .= $this->address_zip.' ';
        }

        return trim($cz);
    }

    public function getHouseholdIDPrettyAttribute()
    {
        return str_replace(0, '<span class="text-blue-lighter">0</span>', $this->household_id);
    }

    public function getDistrictName($code)
    {
        $district = District::where('code', $code)
                    ->where('state', session('team_state'))
                    ->first();

        return $district->name;
    }

    public function houseDistrict()
    {
        return $this->belongsTo(District::class, 'house_district', 'code')
                    ->where('state', session('team_state'))
                    ->where('type', 'H');
    }

    public function senateDistrict()
    {
        return $this->belongsTo(District::class, 'senate_district', 'code')
                    ->where('state', session('team_state'))
                    ->where('type', 'S');
    }

    public function congressDistrict()
    {
        return $this->belongsTo(District::class, 'congress_district', 'code')
                    ->where('state', session('team_state'))
                    ->where('type', 'F');
    }

    public function getPartyFullAttribute()
    {
        switch ($this->party) {

            case 'R':
                return 'Republican';
                break;

            case 'D':
                return 'Democrat';
                break;

            case 'U':
                return 'Unenrolled';
                break;

            default:
                return $this->party;
        }
    }

    public function getElectionsPrettyAttribute()
    {
        return $this->getElectionsPretty($this->elections);
    }

    public function getElectionsPretty($elections)
    {
        //dd($this->elections);
        $formatted = [];

        $all_elections = collect($elections)->sortKeys()->reverse();

        foreach ($all_elections as $race => $vote) {
            $race = explode('-', $race);
            $year = $race[1];
            $month = $race[2] * 1;
            $day = $race[3] * 1;
            $date = $month.'/'.$day.'/'.$year;

            switch ($race[4]) {

                case 'L0000':
                    $city_id = $race[5];
                    $city = Municipality::find($city_id);
                    $jurisdiction = ($city) ? $city->name : null;
                    $type = 'Municipal';
                    break;

                case 'LTM00':
                    $city_id = $race[5];
                    $city = Municipality::find($city_id);
                    $jurisdiction = ($city) ? $city->name : null;
                    $type = 'Town Meeting';
                    break;

                case 'STATE':
                    $jurisdiction = 'State';
                    $type = 'General';
                    break;

                case 'PP000':
                    $jurisdiction = 'State';
                    $type = 'Presidential Primary';
                    break;

                case 'SP000':
                    $jurisdiction = 'State';
                    $type = 'Primary';
                    break;

                case 'SS000':
                    $jurisdiction = 'State';
                    $type = 'Special';
                    break;

                case 'SSP00':
                    $jurisdiction = 'State';
                    $type = 'Special Primary';
                    break;

                case 'L0000':
                    $jurisdiction = 'District';
                    // $district_id = $race[5];
                    // $district = District::find($district_id);
                    // $type = "District ".$district->name;
                    $type = 'Legislative ';
                    break;

                case 'LS000':
                    $jurisdiction = 'District';
                    // $district_id = $race[5];
                    // $district = District::find($district_id);
                    // $type = "District ".$district->name." special";
                    $type = 'Legislative Special';
                    break;

                default:
                    $jurisdiction = null;
                    $type = implode('-', $race);
                    break;
            }

            $race = $type.' ('.$date.')';

            $vote = explode('-', $vote);
            $city_id = $vote[0];
            $registered_as = $vote[1];
            // $registered_as = ($registered_as == 0) ? '?' : $registered_as;
            $voted_as = $vote[2];

            // $location = $city_id;
            $location = null;

            if ($city_id && is_numeric($city_id)) {
                $city = Municipality::where('state', session('team_state'))
                                    ->where('code', $city_id * 1)
                                    ->first();
                if ($city) {
                    $location = $city->name;
                } 
            }

            $formatted[] = ['off_year'      => ($year % 2) ? true : false,
                            'jurisdiction'  => $jurisdiction,
                            'type'          => $type,
                            'registered_as' => $registered_as,
                            'voted_as'      => $voted_as,
                            'date'          => $date,
                            'registered_as' => $registered_as,
                            'location'      => $location,
                            ];
        }
        //dd($formatted);
        return $formatted;
    }

    public function getVoterIdAttribute($voter_id)
    {
        if (! $voter_id) {
            return $this->id;
        }

        return $voter_id;
    }
    public function getFullAddressAttribute($full_address)
    {
        if ($this->address_apt) {
            if (!Str::contains($full_address, '#')) {
                return $this->generateFullAddress();
            }
        }
        return $full_address;
    }
    public function getFullAddressNoZipAttribute()
    {
        $address = ($this->full_address);
        if (is_numeric(substr($address, -5))) {
            return substr($address, 0, -5);
        } else {
            return $address;
        }
    }

    public function getMailingAddressAttribute()
    {
        if ($this->mailing_info) {
            $str = '';
            if (isset($this->mailing_info['address']) && ($this->mailing_info['address'])) {
                $str .= $this->mailing_info['address'].' ';
                if ($this->mailing_info['address2']) {
                    $str .= $this->mailing_info['address2'].' ';
                }
                $str .= $this->mailing_info['city'].', ';
                $str .= $this->mailing_info['state'].' ';
                $str .= $this->mailing_info['zip'];
            }

            return $str;
        }
    }

    public function getIsPersonAttribute()
    {
        $office_team = Auth::user()->team;
        if ($office_team->app_type == 'office') {
            // Used to differentiate Voter and person objects in office app
            return false;
        }
        $office_team = Auth::user()->team->account->teams()->where('app_type', 'office')->first();
        if (! $office_team) {
            return null;
        }
        $person = $office_team->people()->where('voter_id', $this->id)->first();

        return $person;
    }

    public function getTable()
    {
        return session('team_table');
    }

    public function getNameAttribute()
    {
        if (trim($this->full_name_middle)) {
            return $this->full_name_middle;
        } elseif (trim($this->full_name)) {
            return $this->full_name;
        } elseif (trim($this->first_name.' '.$this->last_name)) {
            return trim($this->first_name.' '.$this->last_name);
        } elseif ($this->email) {
            return $this->email;
        }

        return 'UNKNOWN';
    }
    public function getAgeAttribute()
    {
        if ($this->dob) {
            return $this->dob->age;
        }
        if ($this->yob) {
            return (date('Y') - $this->yob - 1).' or '.(date('Y') - $this->yob);
        }
        // if ($this->voter) {
        //     return $this->voter->age;
        // }
    }

    public function ccVoter()
    {
        return $this->belongsTo(Models\CC\CCVoter::class, 'import_order');
    }

    public function ccElectionData()
    {
        return Models\CC\CCElection::where('voter_code', str_replace('MA_', '', $this->id));
    }

    public function getEmailsStringAttribute($email)
    {
        if (is_array($this->emails)) {
            return implode(', ', $this->emails);
        }

        return '';
    }

    // public function getForceEmailsAttribute()
    // {
    //     // This attribute was necessary because sometimes it didn't work to
    //     // get voter->emails ... no idea why
    //     $data = $this->getAttributes();
    //     $emails = (array)  $data['emails'];
    //     if (is_array($emails)) {
    //         return implode(', ', $emails);
    //     }
    //     return $emails;
    // }

    // public function getEmailsAttribute($emails)
    // {
    //     dd($emails);
    //     if (is_array($emails)) {
    //         if (count($emails) > 0) {
    //             return $emails[0];
    //         }
    //     }
    //     return [];
    // }

    public function mightBeArchived()
    {
        return $this->updated_at < Carbon::parse('2017-05-02');
    }

    public function getTitleAttribute()
    {
        if ($this->name_title) {
            return $this->name_title;
        }
        if ($this->gender == 'M') {
            return 'Mr.';
        }
        if ($this->gender == 'F') {
            return 'Ms.';
        }

        return null;
    }

    public function generateHouseholdId()
    {
        //DUPLICATED IN PERSON MODEL
        return strtoupper(substr($this->address_state, 0, 2).'|'.
                Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_street, 20, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
            );
    }
    public function getHouseholdIdAttribute($household_id)
    {
        if ($household_id) {
            return $household_id;
        }
        //DUPLICATED IN PERSON MODEL, VOTER
        return strtoupper(substr($this->address_state, 0, 2).'|'.
                Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_street, 20, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
            );
    }

    public function generateFullAddress()
    {
        //dd($address_apt);
        $address_apt = $this->address_apt;

        if ($address_apt && !Str::contains($address_apt, '#')) {
            $address_apt = '#'.$address_apt;
        }
        $full_address = preg_replace('!\s+!', ' ', //Remove >1 spaces
                            titleCase(
                                (($this->address_number == 0) ? '' : $this->address_number).' '.
                                $this->address_fraction.' '.
                                $this->address_street.' '.
                                $address_apt.' '.
                                $this->address_city
                            ).' '.$this->address_state.' '.$this->address_zip);

        return $full_address;
    }

    public function save(array $options = [])
    {
        // =====================================> Enriched Columns
        $this->full_name = $this->first_name.' '.$this->last_name;
        $this->full_name_middle = $this->first_name.' '.$this->middle_name.' '.$this->last_name;
        $this->household_id = $this->generateHouseholdId();
        $this->full_address = $this->generateFullAddress();
        if ($this->address_lat && $this->address_long) {
            try {
                //$this->location = new Point($this->address_lat, $this->address_long);
            } catch (\Exception $e) {

            }
        }
        parent::save($options);
    }
}
