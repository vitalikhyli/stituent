<?php

namespace App;

use App\Group;
use App\GroupPerson;
use App\Issue;
use App\Voter;
use App\Traits\RecordSignature;
use App\Traits\SharedCasesTrait;
use App\WorkFile;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Crypt;
// use Illuminate\Contracts\Encryption\DecryptException;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\ModelUseIndex;


class Person extends Model
{
    use RecordSignature;
    use SoftDeletes;
    use SharedCasesTrait;
    use ModelUseIndex;

    protected $casts = [
            'other_emails'  => 'array',
            'other_phones'  => 'array',
            'mailing_info'  => 'array',
            'business_info' => 'array',
            'support'       => 'array',
            // 'private'       => 'array',
            'old_private'   => 'array',
            'data_history'  => 'array',
        ];

    protected $dates = ['created_at', 'updated_at', 'dob'];
    protected $appends = ['title', 'email'];

    public $unmergable = ['id',
                          'team_id',
                          'is_household',
                          'household_id',
                          'mass_gis_id',
                          'updated_at',
                          'created_at',
                          'deleted_at',
                          'data_history',
                          'old_cc_id',
                          'old_voter_code',
                          'upload_id',
                          'full_name',
                          'full_name_middle',
                          'created_by',
                          'updated_by',
                          'deleted_by',
                          'old_private'];

    public $booleans = ['master_email_list',
                        'massemail_neversend',
                        'deceased'];

    public $streetSuffixes = [
                        'ALLEY' => 'ALY',
                        'ANNEX' => 'ANX',
                        'ARCADE' => 'ARC',
                        'AVENUE' => 'AVE',
                        'BAYOU' => 'YU',
                        'BEACH' => 'BCH',
                        'BEND' => 'BND',
                        'BLUFF' => 'BLF',
                        'BOTTOM' => 'BTM',
                        'BOULEVARD' => 'BLVD',
                        'BRANCH' => 'BR',
                        'BRIDGE' => 'BRG',
                        'BROOK' => 'BRK',
                        'BURG' => 'BG',
                        'BYPASS' => 'BYP',
                        'CAMP' => 'CP',
                        'CANYON' => 'CYN',
                        'CAPE' => 'CPE',
                        'CAUSEWAY' => 'CSWY',
                        'CENTER' => 'CTR',
                        'CIRCLE' => 'CIR',
                        'CLIFFS' => 'CLFS',
                        'CLUB' => 'CLB',
                        'CORNER' => 'COR',
                        'CORNERS' => 'CORS',
                        'COURSE' => 'CRSE',
                        'COURT' => 'CT',
                        'COURTS' => 'CTS',
                        'COVE' => 'CV',
                        'CREEK' => 'CRK',
                        'CRESCENT' => 'CRES',
                        'CROSSING' => 'XING',
                        'DALE' => 'DL',
                        'DAM' => 'DM',
                        'DIVIDE' => 'DV',
                        'DRIVE' => 'DR',
                        'ESTATES' => 'EST',
                        'EXPRESSWAY' => 'EXPY',
                        'EXTENSION' => 'EXT',
                        'FALL' => 'FALL',
                        'FALLS' => 'FLS',
                        'FERRY' => 'FRY',
                        'FIELD' => 'FLD',
                        'FIELDS' => 'FLDS',
                        'FLATS' => 'FLT',
                        'FORD' => 'FOR',
                        'FOREST' => 'FRST',
                        'FORGE' => 'FGR',
                        'FORK' => 'FORK',
                        'FORKS' => 'FRKS',
                        'FORT' => 'FT',
                        'FREEWAY' => 'FWY',
                        'GARDENS' => 'GDNS',
                        'GATEWAY' => 'GTWY',
                        'GLEN' => 'GLN',
                        'GREEN' => 'GN',
                        'GROVE' => 'GRV',
                        'HARBOR' => 'HBR',
                        'HAVEN' => 'HVN',
                        'HEIGHTS' => 'HTS',
                        'HIGHWAY' => 'HWY',
                        'HILL' => 'HL',
                        'HILLS' => 'HLS',
                        'HOLLOW' => 'HOLW',
                        'INLET' => 'INLT',
                        'ISLAND' => 'IS',
                        'ISLANDS' => 'ISS',
                        'ISLE' => 'ISLE',
                        'JUNCTION' => 'JCT',
                        'KEY' => 'CY',
                        'KNOLLS' => 'KNLS',
                        'LAKE' => 'LK',
                        'LAKES' => 'LKS',
                        'LANDING' => 'LNDG',
                        'LANE' => 'LN',
                        'LIGHT' => 'LGT',
                        'LOAF' => 'LF',
                        'LOCKS' => 'LCKS',
                        'LODGE' => 'LDG',
                        'LOOP' => 'LOOP',
                        'MALL' => 'MALL',
                        'MANOR' => 'MNR',
                        'MEADOWS' => 'MDWS',
                        'MILL' => 'ML',
                        'MILLS' => 'MLS',
                        'MISSION' => 'MSN',
                        'MOUNT' => 'MT',
                        'MOUNTAIN' => 'MTN',
                        'NECK' => 'NCK',
                        'ORCHARD' => 'ORCH',
                        'OVAL' => 'OVAL',
                        'PARK' => 'PARK',
                        'PARKWAY' => 'PKY',
                        'PASS' => 'PASS',
                        'PATH' => 'PATH',
                        'PIKE' => 'PIKE',
                        'PINES' => 'PNES',
                        'PLACE' => 'PL',
                        'PLAIN' => 'PLN',
                        'PLAINS' => 'PLNS',
                        'PLAZA' => 'PLZ',
                        'POINT' => 'PT',
                        'PORT' => 'PRT',
                        'PRAIRIE' => 'PR',
                        'RADIAL' => 'RADL',
                        'RANCH' => 'RNCH',
                        'RAPIDS' => 'RPDS',
                        'REST' => 'RST',
                        'RIDGE' => 'RDG',
                        'RIVER' => 'RIV',
                        'ROAD' => 'RD',
                        'ROW' => 'ROW',
                        'RUN' => 'RUN',
                        'SHOAL' => 'SHL',
                        'SHOALS' => 'SHLS',
                        'SHORE' => 'SHR',
                        'SHORES' => 'SHRS',
                        'SPRING' => 'SPG',
                        'SPRINGS' => 'SPGS',
                        'SPUR' => 'SPUR',
                        'SQUARE' => 'SQ',
                        'STATION' => 'STA',
                        'STRAVENUES' => 'STRA',
                        'STREAM' => 'STRM',
                        'STREET' => 'ST',
                        'SUMMIT' => 'SMT',
                        'TERRACE' => 'TER',
                        'TRACE' => 'TRCE',
                        'TRACK' => 'TRAK',
                        'TRAIL' => 'TRL',
                        'TRAILER' => 'TRLR',
                        'TUNNEL' => 'TUNL',
                        'TURNPIKE' => 'TPKE',
                        'UNION' => 'UN',
                        'VALLEY' => 'VLY',
                        'VIADUCT' => 'VIA',
                        'VIEW' => 'VW',
                        'VILLAGE' => 'VLG',
                        'VILLE' => 'VL',
                        'VISTA' => 'VIS',
                        'WALK' => 'WALK',
                        'WAY' => 'WAY',
                        'WELLS' => 'WLS'];
    ////////////////////////////////////////////////////////////////////////////////////

    public function getHouseholdIDPrettyAttribute()
    {
        // return str_replace(0, '<span class="text-blue-lighter">0</span>', $this->household_id);
        $string = null;
        $highlight = true;
        foreach(str_split($this->household_id) as $c) {
            if ($c == '0' && $highlight) {
                $string .= '<span class="text-grey">0</span>';
            } else {
                $string .= $c;
                if (is_numeric($c)) $highlight = false; // Do not highlight subsequence 0's.
            }
            if ($c == '|') $highlight = true;
        }
        return $string;
    }
    public function getFirstNameAttribute($first_name)
    {
        if ($this->nickname) {
            return $this->nickname;
        }
        return $first_name;
    }
    public function getDobReadableAttribute()
    {
        if ($this->dob) {
            return $this->dob->format('n/j/Y');
        }
        return '';
    }
    public function getSocialMediaLinkedAttribute()
    {
        $str = null;
        if ($this->social_media) {
            $parts = preg_split('/\s+/', $this->social_media);
            foreach ($parts as $key => $value) {
                $value = trim($value);
                if ($value) {
                    if (strpos('a'.$value, 'http') > 0) {
                        $str .= "<a target='_blank' href='$value'>$value</a><br>";
                    } else {
                        if (strpos('a'.$value, 'facebook') > 0 || strpos('a'.$value, 'twitter') > 0) {
                            $value = 'http://'.$value;
                            $str .= "<a target='_blank' href='$value'>$value</a><br>";
                        } else {
                            $str .= $value."<br>";
                        }
                    }
                    
                }
            }
        }
        return $str;
    }
    public function getHouseholdCountAttribute()
    {
        $household_id = $this->household_id;
        if ($household_id) {
            $people = Person::where('team_id', Auth::user()->team->id)
                            ->where('is_household', false)
                            ->where('household_id', $household_id)
                            ->get();

            $voters = Voter::where('household_id', $household_id)
                           ->whereNotIn('id', $people->pluck('voter_id'))
                           ->get();

            $residents = $people->merge($voters);
            return $residents->count();
        }
        return null;
    }
    public function getActivityCountAttribute()
    {
        return $this->cases->count() 
             + $this->contacts->count()
             + $this->groups->count();
    }
    public function getFullNameAttribute($v)
    {
        return $v;
    }
    public function getLatLongAttribute()
    {
        return "".$this->address_lat.$this->address_long;
    }
    public function getActivityIconsAttribute()
    {
        $icons = [];
        for ($i=0; $i<$this->cases()->count(); $i++) {
            $icons[] = 'case';
        }
        for ($i=0; $i<$this->contacts()->count(); $i++) {
            $icons[] = 'note';
        }
        for ($i=0; $i<$this->groups()->count(); $i++) {
            $icons[] = 'group';
        }
        for ($i=0; $i<$this->entities()->count(); $i++) {
            $icons[] = 'org';
        }
        for ($i=0; $i<$this->files()->count(); $i++) {
            $icons[] = 'file';
        }
        return $icons;
    }
    public function getAvatarAttribute($avatar)
    {

        if ($avatar) {
            return $avatar;
        }
        if ($this->email) {

            // $hash = md5(strtolower(trim($this->email)));
            return  "https://unavatar.io/".$this->email."?fallback=https://communityfluency.com/images/avatar.png";
            //return "https://www.gravatar.com/avatar/$hash?d=mp&r=g";
        }
        return '/images/avatar.png';
    }
    public function cfPlus()
    {
        $cfplus = CFPlus::firstWhere('voter_id', $this->voter_id);
        return $cfplus;
    }
    public function getVoterIdOrPersonIdAttribute()
    {
        if ($this->voter_id) {
            return $this->voter_id;
        }
        return $this->id;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voter()
    {
        if (!session('team_table')) {
            return $this->voterMaster();
        }
        return $this->belongsTo(Voter::class);
    }

    public function voterMaster()
    {
        return $this->belongsTo(VoterMaster::class, 'voter_id');
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
        $street_num_town .= $this->address_city;
        return trim($street_num_town);
    }
    public function getAddressStreetAttribute($v)
    {
        return $v;
    }

    public function getAddressCityAttribute($v)
    {
        return $v;
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

    public function getAddressNoStateAttribute()
    {
        $string = trim($this->address_number.' '.$this->address_fraction);
        $string .= ' '.trim($this->address_street.' '.$this->address_apt);
        $string .= ' '.$this->address_city;
        return $string;
    }

    public function getAddressNoCityAttribute()
    {
        $string = trim($this->address_number.' '.$this->address_fraction);
        $string .= ' '.trim($this->address_street.' '.$this->address_apt);
        return $string;
    }

    public function files()
    {
        return $this->belongsToMany(WorkFile::class, 'person_file', 'person_id', 'file_id')
                    ->orderBy('files.name');
    }

    ////////////////////////////////////////////////////////////////////////////////////

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

    public function groupPivot($group_id)
    {
        return GroupPerson::where('person_id', $this->id)->where('group_id', $group_id)->first();
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
    public function getMailingAddressOrResidentialAttribute()
    {
        $address = $this->mailing_address;
        if (!$address) {
            $address = $this->full_address;
        }
        return $address;
    }

    public function getIsPersonAttribute()
    {
        return true;
    }

    public function massGis()
    {
        return $this->belongsTo(MassGIS::class, 'mass_gis_id');
    }

    public function getEmailAttribute()
    {
        if ($this->primary_email) {
            return $this->primary_email;
        }
        if ($this->work_email) {
            return $this->work_email;
        }
        if ($this->other_emails) {
            foreach ($this->other_emails as $email) {
                if (is_array($email)) {
                    if (is_array($email[0])) {
                        return implode(' ', $email[0]);
                    } else {
                        return $email[0];
                    }
                } else {
                    return $email;
                }
            }
        }

        return null;
    }
    public function getPhoneAttribute()
    {
        if ($this->primary_phone) {
            return $this->primary_phone;
        }
        if ($this->work_phone) {
            return $this->work_phone;
        }
        if ($this->other_phones) {
            foreach ($this->other_phones as $phone) {
                if (is_array($phone)) {
                    if (is_array($phone[0])) {
                        return implode(' ', $phone[0]);
                    } else {
                        return $phone[0];
                    }
                } else {
                    return $phone;
                }
            }
        }

        return null;
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
        } elseif ($this->is_household) {
            return $this->address_number.' '.$this->address_street." ".$this->address_city;
        }

        return 'UNKNOWN';
    }
    
    public function scopeNotHouseholds($query)
    {
        return $query->where('is_household', false);
    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }
    public function cohabitators()
    {
        if (!$this->household_id) {
            return collect([]);
        }

        $cohabitors = Person::thisTeam()
                            ->where('household_id', $this->household_id)
                            ->where('id', '<>', $this->id)
                            ->get();

        return $cohabitors;
    }

    public function getAgeAttribute()
    {
        if ($this->dob) {
            return $this->dob->age;
        }
        // if ($this->voter) {
        //     return $this->voter->age;
        // }
    }
    public function getTownAddressAttribute()
    {
        return $this->address_line_street.", ".$this->address_city;
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
    public function getYobAttribute($yob)
    {
        if ($yob) {
            return $yob;
        }
        if ($this->dob) {
            return $this->dob->year;
        }
        if ($this->voter) {
            return $this->voter->yob;
        }
    }

    public static function boot()
    {
        parent::boot();

        // static::updated(function ($model) {
        // 	if ($model->voter_id == null) {
        //     	$model->voter_id = 'internal:'.$model->id;
        //     	$model->save();
        //     }
        // });

        // static::created(function ($model) {
        //     if ($model->voter_id == null) {
        //     	$model->voter_id = 'internal:'.$model->id;
        //     	$model->save();
        //     }
        // });
    }

    public function save(array $options = [])
    {


        if (!$this->data_history) {
            $arr = [];
            $data_history = [];
            $arr[date('Y-m-d')." 00:00:00"] = $this->getOriginal();
            $data_history[] = $arr;
            $this->data_history = $data_history;
        }
        //dd($this);
        
        // =====================================> Enriched Columns
        $this->full_name = $this->first_name.' '.$this->last_name;

        if (! $this->middle_name) {
            $this->full_name_middle = $this->full_name;
        } else {
            $this->full_name_middle = $this->first_name.' '.$this->middle_name.' '.$this->last_name;
        }
        $this->household_id = $this->generateHouseholdId();
        $this->full_address = $this->generateFullAddress();

        if (! $this->city_code) {
            if ($this->address_city) {
                $city = Municipality::where('name', $this->address_city)->first();
                if ($city) {
                    $this->city_code = $city->code;
                }
            }
        }

        if ($this->dob && !$this->birthday) {
            $this->birthday = $this->dob->format('m-d');
        }

        // =====================================> DATA HISTORY TRACK CHANGES
        $ignored_columns = ['data_history', 'created_at', 'updated_at', 'yob'];
        $new_vals = collect($this->getAttributes())->except($ignored_columns);
        $original = collect($this->getOriginal())->except($ignored_columns);
        $changed_arr = [];
        foreach ($new_vals as $field => $val) {
            if (!isset($original[$field])) {
                continue;
            }
            if (is_array($original[$field])) {
                //dd($new_vals[$field], json_encode($original[$field]));
                if ($new_vals[$field] != json_encode($original[$field])) {
                    $changed_arr[$field] = $val;
                }
            } else if ($field == 'private') {
                $value = $val;
                for ($i = 0; $i < 3; $i++) {
                    $value = base64_decode($value);
                }
                if ($value != $original[$field]) {
                    $changed_arr[$field] = $val;
                }
                //dd($val, $value, $original[$field]);
            } else {
                if ($new_vals[$field] != $original[$field]) {
                    $changed_arr[$field] = $val;
                }
            }
        }
        if (count($changed_arr) > 0) {
            $data_history = $this->data_history;
            $arr = [];
            $arr[date('Y-m-d H:i:s')] = $changed_arr;
            if (Auth::user()) {
                $arr['data_history_user_id'] = Auth::user()->id;
            }
            $data_history[] = $arr;
            $this->data_history = $data_history;
            //dd($changed_arr);
        }
        //dd($original, $new_vals, $changed_arr);

        parent::save($options);
    }

    public function updateMergeLog($two, $merge_log)
    {
        if (!$this->data_history) {
            $arr = [];
            $data_history = [];
            $arr[date('Y-m-d')." 00:00:00"] = $this->getOriginal();
            $data_history[] = $arr;
            $this->data_history = $data_history;
        }

        $history = $this->data_history;
        $history['merged-'.$two->id.'-'.date('Y-m-d')] = $merge_log;
        $this->data_history = $history;
        $this->save();
    }

    public function getElectionsPrettyAttribute()
    {
        //dd("Laz");
        if ($this->voter_id) {
            if ($voter = Voter::find($this->voter_id)) {
                return $voter->elections_pretty;
            }
        }

        return [];
    }

    public function related_people()
    {

        //One Direction
        $people = DB::table('relationships')->select('people.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('subject_id', $this->id)
                                              ->where('subject_type', 'p')
                                              ->where('object_type', 'p')
                                              ->join('people', 'relationships.object_id', 'people.id')
                                              ->orderBy('people.last_name')
                                              ->get();

        return $people;
    }

    public function related_people_reverse()
    {
        $people = DB::table('relationships')->select('people.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('object_id', $this->id)
                                              ->where('subject_type', 'p')
                                              ->where('object_type', 'p')
                                              ->join('people', 'relationships.subject_id', 'people.id')
                                              ->orderBy('people.last_name')
                                              ->get();

        return $people;
    }

    //    public function related_entities()
    //    {
    //        $entities = DB::table('relationships')->select('entities.*',
    //                                                    'relationships.kind',
    //                                                    'relationships.id as relationship_id')
    //                                              ->where('subject_id',$this->id)
    //                                              ->where('subject_type','p')
    //                                              ->where('object_type','e')
    //                                              ->join('entities','relationships.object_id','entities.id')
    //                                              ->orderBy('entities.name')
    //                                              ->get();
    //     return $entities;
    // }

    //    public function related_entities_reverse()
    //    {
    //        $entities = DB::table('relationships')->select('entities.*',
    //                                                    'relationships.kind',
    //                                                    'relationships.id as relationship_id')
    //                                              ->where('object_id',$this->id)
    //                                              ->where('subject_type','e')
    //                                              ->where('object_type','p')
    //                                              ->join('entities','relationships.subject_id','entities.id')
    //                                              ->orderBy('entities.name')
    //                                              ->get();
    //        return $entities;
    //    }

    public function entities()
    {
        return $this->belongsToMany(Entity::class)->withPivot('relationship', 'id');
    }

    public function generateFullAddress()
    {
        $full_address = preg_replace('!\s+!', ' ', //Remove >1 spaces
                            titleCase(
                                (($this->address_number == 0) ? '' : $this->address_number).' '.
                                $this->address_fraction.' '.
                                $this->address_street.' '.
                                $this->address_apt.' '.
                                $this->address_city
                            ).' '.$this->address_state.' '.$this->address_zip);

        return trim($full_address);
    }

    public function generateHouseholdId()
    {
        $street_words = array_reverse(explode(' ', strtoupper($this->address_street)));
        foreach ($street_words as $w => $word) {
            if (isset($this->streetSuffixes[$word])) {
                $street_words[$w] = $this->streetSuffixes[$word];
                break; // Change only the first matching word, starting from the end
            }
        }
        $street_standard = implode(' ', array_reverse($street_words));

        $id = null;
        
        if (
            $this->address_state &&
            $this->address_city &&
            $this->address_street &&
            $this->address_number
            ) {
            $id = strtoupper(substr(str_pad($this->address_state, 2, 'Z', STR_PAD_LEFT), 0, 2).'|'.
                        Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
                        Str::slug(str_pad($street_standard, 20, '0', STR_PAD_RIGHT)).'|'.
                        Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
                        Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
                        Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
                    );
        }

        return $id;
    }

    public function generateFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function generateFullNameMiddle()
    {
        return $this->first_name.' '.trim($this->middle_name.' ').$this->last_name;
    }

    public function memberOfGroup($group_id)
    {
        if (GroupPerson::where('group_id', $group_id)->where('person_id', $this->id)->exists()) {
            return true;
        } else {
            return false;
        }
    }

    public function groupSupport($group_id)
    {
        $pivot = GroupPerson::where('group_id', $group_id)->where('person_id', $this->id)->first();
        return ($pivot) ? $pivot->position : null;
    }
    public function getPositionAttribute()
    {
        if (isset($this->pivot->position)) {
            return ucwords($this->pivot->position);
        }
        return "";
    }

    public function entity_members()
    {
        return self::select('people.*', 'entities_people.affiliation')
                          ->join('entities_people', 'people.id', '=', 'entities_people.person_id')
                          ->where('entities_people.entity_id', $this->id)
                          ->get();
        // return $this->hasMany('Person', 'entities_people', 'person_id', 'id', 'entity_id', 'id');
    }

    public function issues()
    {
        return $this->belongsToMany(Issue::class)->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)
                    ->using(GroupPerson::class) //Added this
                    ->withPivot('position', 'title', 'notes', 'id', 'created_by', 'updated_by')
                    ->withTimestamps();
    }

    public function cases()
    {
        return $this->belongsToMany(WorkCase::class, 'case_person', 'person_id', 'case_id')->withTimestamps();
    }
    public function sharedCases()
    {
        $shared_cases = $this->getSharedCases($this->voter_id);
        return SharedCase::whereIn('id', $shared_cases->pluck('id'));
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class)->withTimestamps();
    }
    public function contactsAuth()
    {
        $contact_ids = $this->contactPerson()->pluck('contact_id');
        return Auth::user()->contacts()->whereIn('id', $contact_ids);
    }

    public function contactPerson()
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function groupPerson()
    {
        return $this->hasMany(GroupPerson::class);
    }
    public function casePerson()
    {
        return $this->hasMany(CasePerson::class);
    }


    public function team()
    {
        return $this->belongsTo(Team::class, $this->current_team_id);
    }

    protected $guarded = [

    ];

    //////////////////////////////////////////////////////////////////////////////

    // public function getEmailsAttribute($value)
    // {
    //     return json_decode($value);
    // }

    // public function setEmailsAttribute($value)
    // {
    //     $this->attributes['emails'] = json_encode($value);
    // }

    //////////////////////////////////////////////////////////////////////////////

    public function getSupportAttribute($value)
    {
        return json_decode($value);
    }

    public function setSupportAttribute($value)
    {
        $this->attributes['support'] = json_encode($value);
    }

    //////////////////////////////////////////////////////////////////////////////

    public function getPrivateAttribute($value)
    {
        for ($i = 0; $i < 3; $i++) {
            $value = base64_decode($value);
        }

        return $value;
    }

    public function bulkEmails()
    {
        return $this->hasMany(BulkEmailQueue::class);
    }

    public function setPrivateAttribute($value)
    {
        for ($i = 0; $i < 3; $i++) {
            $value = base64_encode($value);
        }
        $this->attributes['private'] = $value;
    }
}
