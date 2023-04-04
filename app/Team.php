<?php

namespace App;

use App\Directory;
use Auth;
use DB;
// use Intervention\Image\Facades\Image;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Storage;
use Illuminate\Support\Facades\Storage;
use Mpociot\Teamwork\Models\TeamworkTeam;

class Team extends TeamworkTeam
{
    use HasFactory;

    public function participantsWithNonmatchingCityCodes()
    {
        $problems = [];
        
        foreach(Participant::where('team_id', $this->id)->get() as $participant) {

            // if (!$participant->voterMaster) continue;
            // if ($participant->city_code != $participant->voterMaster->city_code) {
            //     $problems[] = $participant;
            // }

            if(!$participant->municipality) continue;

            if ($participant->municipality->name != $participant->address_city) {
                $problems[] = $participant;
            }
        }

        return collect($problems);
    }

    public function hasPresetCats()
    {
        return ($this->categories()
                     ->where('can_edit', false)
                     ->whereIn('name', ['Legislation', 
                                        'Issue Groups', 
                                        'Constituent Groups'])
                     ->count() == 3);
    }

    public function getShortNameAttribute($value)
    {
        if ($this->attributes['short_name'] != null) {
            return $this->attributes['short_name'];
        } else {
            return substr($this->name, 0, 20);
        }
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function userImports()
    {
        return $this->hasMany(UserUpload::class);
    }

    public function campaignLists()
    {
        return $this->hasMany(CampaignList::class);
    }

    public function defaultDirectory()
    {
        $dir = Directory::where('team_id', $this->id)->whereNull('parent_id')->first();
        if (!$dir) {
            $dir = new Directory;
            $dir->name = Auth::user()->team->name;
            $dir->team_id = Auth::user()->team_id;
            $dir->save();
        }
        return $dir->id;
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function groupPerson()
    {
        return $this->hasMany(GroupPerson::class);
    }

    public function contactTypes()
    {
        $presets = ['emailed', 'called', 'visited'];

        $others = \App\Contact::select('type')
                              ->where('team_id', $this->id)
                              ->whereNotIn('type', $presets)
                              ->groupBy('type')
                              ->pluck('type')
                              ->toArray();

        sort($others, SORT_NATURAL | SORT_FLAG_CASE);

        $all = array_merge($presets, $others);

        return $all;
    }

    public function fa_logo()
    {
        if ($this->app_type == 'campaign') {
            return 'fas fa-flag';
        }
        if ($this->app_type == 'office') {
            return 'fas fa-landmark';
        }
        if ($this->app_type == 'u') {
            return 'fas fa-graduation-cap';
        }
        if ($this->app_type == 'nonprofit') {
            return 'fas fa-leaf';
        }
        if ($this->app_type == 'business') {
            return 'fas fa-building';
        }
    }
    public function actions()
    {
        return $this->hasMany(Action::class);
    }
    public function events()
    {
        return $this->hasMany(CampaignEvent::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'current_team_id');
    }

    public function activeUsers()
    {
        return $this->users()->where('active', true);
    }

    public function usersAll()
    {
        // return $this->hasManyThrough(User::class, TeamUser::class);
        return $this->belongsToMany(User::class)->using(TeamUser::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function groups()
    {
        return $this->hasManyThrough(Group::class, Category::class);
    }


    public function contacts()
    {
        return $this->hasMany(Contact::class)
                    ->where('private', false);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    public function cases()
    {
        return $this->hasMany(WorkCase::class);
    }

    public function searches()
    {
        return $this->hasMany(Search::class);
    }

    public function dataUpdates()
    {
        return $this->hasMany(DataUpdate::class);
    }

    public function getVoterTableAttribute()
    {
        return 'x_voters_'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getHouseholdTableAttribute()
    {
        return 'x_households_'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getTeamPaddedAttribute()
    {
        return 'team_'.str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getPublicFolderAttribute()
    {
        $folder = storage_path().'/app/public/user_uploads/'.Auth::user()->team->team_padded;
        if (! file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        return $folder;
    }

    public function getPublicUrlAttribute()
    {
        return '/storage/user_uploads/'.Auth::user()->team->team_padded;
    }

    public function getUserFilesFolderAttribute()
    {
        $teamfolder = 'team_'.str_pad($this->id, 5, '0', STR_PAD_LEFT);
        $fullpath = storage_path().'/app/user_files/'.$this->app_type.'/'.$teamfolder;
        if (! file_exists($fullpath)) {
            mkdir($fullpath, 0755, true);
        }

        return $fullpath;
    }

    public function refreshCount()
    {
        $slice = VoterSlice::whereName($this->db_slice)->first();
        if ($slice) {
            $voters_count = $slice->voters_count;
            $people_count = $this->people()->whereNull('voter_id')->count();
            $totals_count = $voters_count + $people_count;
            $this->constituents_count = $totals_count;
            $this->save();
        } else {
            $voters_count = DB::table($this->db_slice)->count();
            $people_count = $this->people()->whereNull('voter_id')->count();
            $totals_count = $voters_count + $people_count;
            $this->constituents_count = $totals_count;
            $this->save();
        }
    }

    public function getConstituentsCountAttribute($count)
    {
        if ($count < 20) {
            $this->refreshCount();
        }

        return $count;
    }
    public function getUnarchivedCountAttribute()
    {
        $slice = VoterSlice::whereName($this->db_slice)->first();
        if ($slice) { 
            if ($slice->unarchived_count) {
                return $slice->unarchived_count;
            } else {
                $unarchived_count = Voter::whereNull('archived_at')->count();
                $slice->unarchived_count = $unarchived_count;
                $slice->save();
                return $unarchived_count;
            }
        }
    }
}
