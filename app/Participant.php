<?php

namespace App;

use App\CampaignEvent;
use App\CampaignEventInvite;
use App\CampaignParticipant;
use App\Municipality;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use DB;
use App\Participant;

class Participant extends Model
{
    protected $casts = [
            'other_emails'  => 'array',
            'other_phones'  => 'array',
        ];


    

    public function cohabitators()
    {
        if (!$this->voter_id) {
            return collect([]);
        }
        
        if ($this->voter) {
            return $this->voter->cohabitators();
        }

        return collect([]);
    }

    public function volunteerModel()
    {
        return $this->hasOne(\App\Models\Campaign\Volunteer::class);
    }


    public function getVolunteeringAttribute()
    {
        $str = "";
        // dd($this->volunteer);
        if ($cp = $this->campaignParticipant) {
            foreach ($this->getVolunteerColumns() as $col) {
                if ($cp->$col) {
                    $str .= strtoupper(str_replace('volunteer_', '', $col))."\n";
                }
            }
        }
        return $str;
    }

    public function getMunicipalityAttribute()
    {
        $municipality = Municipality::where('code', $this->city_code)->first();
        if ($municipality) return $municipality;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function relatedUser()
    {
        return $this->belongsToMany(User::class, 'list_user', 'participant_id', 'user_id');
    }

    public function listsTheyBelongTo()
    {
        if ($this->voter_id) {
            if ($this->voter) {
                return $this->voter->listsTheyBelongTo();
            }
        }
    }


    // public function getVoterIDAttribute($voter_id)
    // {
    //     if (!$voter_id) return 'CF_'.$this->id;
    // }
    public function cfPlus()
    {
        $cfplus = CFPlus::firstWhere('voter_id', $this->voter_id);
        return $cfplus;
    }

    public function markAsVolunteer($field)
    {
        $field = str_replace('volunteer_', '', $field);
        // to make sure it finds the class_parents(class)
        $tempthis = Participant::find($this->id);
        $cp = $tempthis->campaignParticipant;
        if (!$cp) {
            $cp = new CampaignParticipant;
            $cp->team_id        = Auth::user()->team->id;
            $cp->participant_id = $this->id;
            $cp->voter_id       = $this->voter_id;
            $cp->user_id        = Auth::user()->id;
            $cp->campaign_id    = CurrentCampaign()->id;
        }

        $cp->{ 'volunteer_'.$field } = true;
        $cp->save();
    }
    public function actions()
    {
        return $this->hasMany(Action::class);
    }
    public function getNameAttribute()
    {
        if (!trim($this->full_name)) {
            $this->fillInParticipantFromVoter();
        }
        return $this->first_name.' '.$this->last_name;
    }

    public function fillInParticipantFromVoter()
    {
        if ($this->voter) {
            $fields = [ 'first_name',
                        'last_name',
                        'address_number',
                        'address_street',
                        'address_apt',
                        'address_city',
                        'address_state',
                        'address_zip',
                    ];
            foreach ($fields as $field) {
                if (!$this->$field) {
                    $this->$field = $this->voter->$field;
                }
            }
            $this->save();
        }
    }

    public function getPhoneAttribute()
    {
        if ($this->primary_phone) {
            return $this->primary_phone;
        }
        if ($this->cell_phone) {
            return $this->cell_phone;
        }
        if ($this->other_phones) {
            if (isset($this->other_phones[0][0])) {
                return $this->other_phones[0][0];
            }
        }
        if ($this->work_phone) {
            return $this->work_phone;
        }
    }
    public function getPhonesAttribute()
    {
        $phones = [];
        if (is_numeric($this->id)) {
            //dd($this);
            //dd($this->voter_id);
            if ($this->voter_id) {
                if ($this->voter) {
                    if ($this->voter->home_phone) {
                        $phones['voter_file'] = $this->voter->home_phone;
                    }
                }
            }
        } else {
            // is voter i.e. MA_JNCCN
            //dd($this);
            if ($this->home_phone) {
                $phones['voter_file'] = $this->voter->home_phone;
            }
        }
        if ($this->primary_phone) {
            $phones['primary'] = $this->primary_phone;
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

    // public function getPrimaryEmailAttribute($email)
    // {
    //     if ($email) {
    //         return $email;
    //     }
    //     try {
    //         if ($this->voter) {
    //             //dd($this->voter->emails);
    //             return $this->voter->emails_string;
    //         }
    //     } catch (\Exception $e) {
            
    //     }
    // }

    public function getEmailAttribute()
    {
        //dd($this->primary_email);
        if ($this->primary_email) {
            return $this->primary_email;
        }
        if ($this->other_emails) {
            if (isset($this->other_emails[0][0])) {
                return $this->other_emails[0][0];
            }
        }
        if ($this->work_email) {
            return $this->work_email;
        }
    } 
    public function getEmailsAttribute()
    {
        $emails = [];
        //dd($this->primary_email);
        if ($this->primary_email) {
            $emails['primary'] = $this->primary_email;
        }
        if ($this->other_emails) {
            if (isset($this->other_emails[0][0])) {
                $emails['other'] = $this->other_emails[0][0];
            }
        }
        if ($this->work_email) {
            $emails['work'] = $this->work_email;
        }
        return $emails;
    } 

    public function getHouseholdIdAttribute()
    {
        //DUPLICATED IN PERSON MODEL, VOTER
        return strtoupper(substr($this->address_state, 0, 2).'|'.
                Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_street, 20, '0', STR_PAD_RIGHT)).'|'.
                Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
                Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
            );
    }

    static function getVolunteerColumns($english = null)
    {
        // volunteer_house_party   tinyint(1)  NO      0   
        // volunteer_table tinyint(1)  NO      0   
        // volunteer_lawnsign  tinyint(1)  NO      0   
        // volunteer_general   tinyint(1)  NO      0   
        // volunteer_door_knock    tinyint(1)  NO      0   
        // volunteer_phone_calls   tinyint(1)  NO      0   
        // volunteer_hold_signs    tinyint(1)  NO      0   
        // volunteer_election_day  tinyint(1)  NO      0   
        // volunteer_office_work   tinyint(1)  NO      0   
        // volunteer_write_letters tinyint(1)  NO      0   
        // volunteer_caravan   tinyint(1)  NO      0   
        // volunteer_lit_drop  tinyint(1)  NO      0   
        // volunteer_poll_watch    tinyint(1)  NO      0   
        // volunteer_ward_chair    tinyint(1)  NO      0   
        // volunteer_signatures    tinyint(1)  NO      0   
        // volunteer_other tinyint(1)  NO      0  


        $fields = collect([
            'volunteer_house_party',
            'volunteer_table',
            'volunteer_lawnsign',
            'volunteer_general',
            'volunteer_door_knock',
            'volunteer_phone_calls',
            'volunteer_hold_signs',
            'volunteer_election_day',
            'volunteer_office_work',
            'volunteer_write_letters',
            'volunteer_caravan',
            'volunteer_lit_drop',
            'volunteer_poll_watch',
            'volunteer_ward_chair',
            'volunteer_signatures',
            'volunteer_other',
            'volunteer_website_list',
        ])->sort()->toArray();

        if ($english) {
            $fields = collect($fields)->flip()
                        ->map(function ($item, $key) {
                            $value = str_replace('volunteer_', '', $key);
                            $value = str_replace('_', ' ', $value);
                            $value = ucwords($value);
                            return $value;
                        })->toArray();
        }

        return $fields;
    }

    public function scopeVolunteers($query, $columns = null)
    {
        if (! $columns) {
            $columns = $this->getVolunteerColumns();
        }
        $volunteer_ids = CampaignParticipant::thisCampaign()
                    ->where(function ($q) use ($columns) {
                        foreach ($columns as $col) {
                            $q->orWhere($col, true);
                        }
                    })
                    ->pluck('participant_id');

        return $query->whereIn('id', $volunteer_ids);
    }

    public function getVolunteerAttribute()
    {
        $volunteer = CampaignParticipant::thisCampaign()
                    ->where('participant_id', $this->id)
                    ->where(function ($q) {
                        foreach ($this->getVolunteerColumns() as $col) {
                            $q->orWhere($col, true);
                        }
                    })
                    ->first();

        return ($volunteer) ? true : false;
    }

    public function taggedWith($tag)
    {
        return $this->tags->contains($tag);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function addToAudit($str)
    {
        $this->audit = $this->audit.date('Y-m-d H:i:s').' - '.$str."\n";
        $this->save();
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

    public function event($id)
    {
        $pivot = CampaignEventInvite::where('participant_id', $this->id)
                                    ->where('campaign_event_id', $id)
                                    ->first();

        return $pivot;
    }

    public function scopeNewThisWeek($query)
    {
        return $query->where('created_at', '>=', Carbon::today()->subWeek());
    }

    public function save(array $options = [])
    {
        // Make this work on update, also ???

        $this->full_name = $this->first_name.' '.$this->last_name;

        $this->full_address = $this->address_number
                              .trim(' '.$this->fraction)
                              .' '.$this->address_street
                              .' '.$this->address_apt
                              .' '.$this->address_city
                              .' '.$this->address_state
                              .' '.$this->address_zip;

        if ($this->address_city) {
            $city = Municipality::where('name', $this->address_city)->first();
            if ($city) {
                $this->city_code = $city->code;
            }
        }

        parent::save($options);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function invites()
    {
        return $this->hasMany(CampaignEventInvite::class);
    }

    public function voter()
    {
        if (! $this->voter_id) {
            return $this->hasOne(self::class, 'id');
        }
        return $this->belongsTo(Voter::class);
    }

    public function voterMaster()
    {
        if (! $this->voter_id) {
            return $this->hasOne(self::class, 'id');
        }

        return $this->belongsTo(VoterMaster::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function campaignParticipant()
    {
        $campaign = CurrentCampaign();

        return $this->hasOne(CampaignParticipant::class)
                    ->where('campaign_id', $campaign->id);
    }

    // public function campaign_participant($campaign = null) {
    //     if (!$campaign) $campaign = CurrentCampaign();
    //     return CampaignParticipant::where('participant_id', $this->id)
    //                               ->where('campaign_id', $campaign->id)
    //                               ->first();
    // }

    public function support($campaign = null)
    {
        if (! $campaign) {
            $campaign = CurrentCampaign();
        }
        $pivot = CampaignParticipant::where('participant_id', $this->id)
                                     ->where('campaign_id', $campaign->id)
                                     ->first();

        return (! $pivot) ? null : $pivot->support;
    }

    public function notes($campaign = null)
    {
        if (! $campaign) {
            $campaign = CurrentCampaign();
        }
        $pivot = CampaignParticipant::where('participant_id', $this->id)
                                     ->where('campaign_id', $campaign->id)
                                     ->first();

        return (! $pivot) ? null : $pivot->notes;
    }


    public function getSupportAttribute()
    {
        return $this->support();
    }

    public function pivotID($campaign)
    {
        $pivot = CampaignParticipant::where('participant_id', $this->id)
                                     ->where('campaign_id', $campaign->id)
                                     ->first();

        return (! $pivot) ? null : $pivot->id;
    }

    ////////////////////////   ALL TIME, ANY CAMPAIGN VOLUNTEERING   //////////////////////

    public function didTheyEverVolunteerTo($task)
    {
        $pivot = CampaignParticipant::where('participant_id', $this->id)
                                     ->where('volunteer_'.$task, true)
                                     ->first();

        return ($pivot) ? true : false;
    }

    public function getCityStateAttribute()
    {
        return $this->address_city.', '.$this->address_state;
    }
}
