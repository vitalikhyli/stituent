<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class SharedCase extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getNameAttribute()
    {
    	if ($this->shared_type == 'user') {
    		return $this->sharedUser->name;
    	}
    	if ($this->shared_type == 'team') {
    		return $this->sharedTeam->name;
    	}
    }
    public function sharedTeam()
    {
    	return $this->belongsTo(Team::class, 'shared_team_id');
    }
    public function sharedUser()
    {
    	return $this->belongsTo(User::class, 'shared_user_id');
    }
    public function team()
    {
    	return $this->belongsTo(Team::class);
    }
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
    public function case()
    {
    	return $this->belongsTo(WorkCase::class, 'case_id');
    }
    public function scopeSharedWithMe($query)
    {
        $shared_team = SharedCase::where('shared_type', 'team')
                                 ->where('shared_team_id', Auth::user()->team_id)
                                 ->get();

        $shared_user = SharedCase::where('shared_type', 'user')
                                 ->where('shared_user_id', Auth::user()->id)
                                 ->get();

        $shared_ids = $shared_team->merge($shared_user)->unique('case_id')->pluck('id');
        return $query->whereIn('id', $shared_ids);
    }
}
