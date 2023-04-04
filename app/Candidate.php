<?php

namespace App;

use App\CandidateContact;
use App\District;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $connection = 'candidates';
    protected $casts = ['state_data' => 'array'];

    public function marketing()
    {
        return $this->setConnection('main')
                    ->hasOne(CandidateMarketing::class);
    }

    public function votersWithThisName()
    {
        $words = explode(' ', trim($this->address_street));
        $words = array_splice($words, 0, 3);
        if (isset($words[2])) {
            $words[2] = substr($words[2], 0, 1); // Only first two of last word ("Avenue" -> "A")
        }
        $the_short_address = implode(' ', $words);

        // return \App\VoterMaster::orWhere(function($q) {
        //                             $q->where('first_name', $this->first_name);
        //                             $q->where('last_name', $this->last_name);
        //                             $q->where('address_city', $this->address_city);
        //                         })->orWhere(function($q) use ($the_short_address) {
        //                             $q->where('last_name', $this->last_name);
        //                             $q->where('full_address', 'LIKE', $the_short_address.'%');
        //                         })
        //                         ->get();

        $voters = \App\VoterMaster::where('last_name', $this->last_name)
                               ->where('full_address', 'LIKE', $the_short_address.'%')
                                ->get();

        $voters = $voters->each(function ($item) {
            $item->match_score = similar_text($item->full_address, $this->address_street) + similar_text($item->full_name_middle, $this->fullname);
        })->sortByDesc('match_score');

        return $voters;

        // return \App\VoterMaster::where('full_address', 'LIKE', $the_short_address.'%')
        //                         ->get();

        // return \App\VoterMaster::where('last_name', $this->last_name)
        //                        ->where('address_city', $this->address_city)
        //                        ->get();
    }

    public function getDistrictOrCityAttribute()
    {
        if ($this->district) {
            return $this->district;
        }
        if ($this->theDistrict) {
            return $this->theDistrict->name;
        }
        if ($this->theCity) {
            return $this->theCity->name;
        }
    }

    public function getAnyEmailAttribute()
    {
        if ($this->candidate_email && $this->ok_email_candidate) {
            return $this->candidate_email;
        }
        if ($this->chair_email && $this->ok_email_chair) {
            return $this->chair_email;
        }
        if ($this->treasurer_email && $this->ok_email_treasurer) {
            return $this->treasurer_email;
        }
    }

    public function theCity()
    {
        return $this->hasOne(Municipality::class, 'id', 'municipality_id');
    }

    public function theDistrict()
    {
        return $this->hasOne(District::class, 'id', 'district_id')
                    ->where('state', session('team_state'));;
    }

    public function voter()
    {
        return $this->hasOne(VoterMaster::class, 'id', 'voter_id');
    }

    public function contacts()
    {
        return $this->hasMany(CandidateContact::class);
    }

    public function getShortPartyAttribute()
    {
        return substr($this->party, 0, 1);
    }

    public function getFullOfficeAttribute()
    {
        if ($this->office == 'House') {
            return 'State Representative';
        }
        if ($this->office == 'Senate') {
            return 'State Senator';
        }

        return $this->office;
    }

    public function addSequenceStep($sequence, $step, $mailable = null)
    {
        $contact = new CandidateContact;
        $contact->candidate_id = $this->id;
        $contact->type = 'email';
        $contact->sequence = $sequence;
        $contact->step = $step;
        $contact->mailable = $mailable;
        $contact->save();
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
