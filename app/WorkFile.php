<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use App\SharedCase;

class WorkFile extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $table = 'files';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userCanAccess($user)
    {
        if ($this->user_id == $user->id) {
            return true;
        }
        if ($this->team_id == $user->team_id) {
            return true;
        }
        if ($this->cases()->count() > 0) {
            $case_ids = $this->cases()->pluck('case_id');
            $usercases_ids = Auth::user()->cases()->pluck('id');
            $shared_team = SharedCase::where('shared_type', 'team')
                                 ->where('shared_team_id', $user->team_id)
                                 ->pluck('case_id');

            $shared_user = SharedCase::where('shared_type', 'user')
                                 ->where('shared_user_id', $user->id)
                                 ->pluck('case_id');

            $all_valid = $usercases_ids->merge($shared_team)->merge($shared_user);
            //dd($all_valid);

            foreach ($case_ids as $case_id) {
                if ($all_valid->contains($case_id)) {
                    return true;
                }
            }
        }
        return false;
        
    }
    public function currentUserCanAccess()
    {
        return $this->userCanAccess(Auth::user());
    }

    public function cases()
    {
        return $this->belongsToMany(WorkCase::class, 'case_file', 'file_id', 'case_id')
                    ->whereNull('case_file.deleted_at');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_file', 'file_id', 'group_id')
                    ->whereNull('group_file.deleted_at');
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'person_file', 'file_id', 'person_id');
    }
    public function scopeThisTeam($q)
    {
        return $q->where('team_id', Auth::user()->team_id);
    }
    public function getFolderNameAttribute()
    {
        return $this->directory->name;
    }
    public function directory()
    {
        return $this->belongsTo(Directory::class);
    }
}
