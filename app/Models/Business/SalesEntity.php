<?php

namespace App\Models\Business;

use App\Contact;
use Auth;
use Illuminate\Database\Eloquent\Model;

class SalesEntity extends Model
{
    protected $table = 'sales_entities';

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function entity()
    {
        return $this->belongsTo(\App\Entity::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function pattern()
    {
        return $this->hasOne(\App\Models\Business\SalesPattern::class, 'id', 'pattern_id');
    }

    public function getHighestStepAttribute()
    {
        $entity_contacts = $this->entity->contacts->pluck('id')->toArray();

        $steps = SalesContact::whereIn('contact_id',
                                        $entity_contacts
                                      )->get()
                                       ->pluck('step')
                                       ->toArray();

        if (! $steps) {
            return 0;
        }
        if (! isset($this->pattern)) {
            return 0;
        }
        if (! isset($this->pattern->steps)) {
            return 0;
        }

        return $this->pattern->steps->whereIn('name', $steps)->max('the_order');
    }

    public function getProgressPercentageAttribute()
    {
        if (! isset($this->pattern->steps)) {
            return 0;
        }

        return round($this->highestStep / $this->pattern->steps->count() * 100, 0);
    }

    public function getLastCheckInAttribute()
    {
        $entity_contacts = $this->entity->contacts->pluck('id')->toArray();

        $checkins = SalesContact::whereIn('contact_id',
                                        $entity_contacts
                                      )->where('check_in', true)
                                        ->pluck('id')
                                        ->toArray();

        return Contact::whereIn('id', $checkins)->max('date');
    }

    public function getNextStepAttribute()
    {
    }
}
