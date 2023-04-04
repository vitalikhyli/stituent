<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class WebSignup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = ['meta' => 'array', 'data' => 'array'];

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function webForm()
    {
        return $this->belongsTo(WebForm::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
    
    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
    
}