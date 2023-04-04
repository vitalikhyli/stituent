<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DateTimeInterface;
use Auth;

class Action extends Model
{
	use SoftDeletes;

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
	public function participant()
	{
		return $this->belongsTo(Participant::class);
	}
	public function voter()
	{
		return $this->belongsTo(Voter::class);
	}
	public function user()
	{
		return $this->belongsTo(User::class);
	}
	public function getAddedByAttribute()
	{
		if ($this->user) {
			return $this->user->name;
		}
	}
}
