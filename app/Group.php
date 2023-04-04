<?php

namespace App;

use App\Traits\RecordSignature;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Group extends Model
{
    use RecordSignature;
    use SoftDeletes;

    public $table = 'groups';

    protected $primaryKey = 'id';

    protected $casts = ['additional_info' => 'array'];

    protected $dates = ['created_at', 'updated_at', 'archived_at'];

    // protected function scopeUnarchived($query)
    // {
    //     return $query->whereNull('archived_at');
    // }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function updatePeopleCounts()
    {
        $this->people_count = $this->groupPerson()->count();
        $this->save();
    }
    public function getUpdatedRecentlyAttribute()
    {
        $latest = $this->groupPerson()->latest()->first();
        if (!$latest) {
            $date = $this->updated_at;
        } else {
            $date = $latest->created_at;
        }
        if ($date > Carbon::today()->subMonths(3)) {
            return true;
        }
        return false;
    }
    public function scopeCurrentTeam($query)
    {
        return $query->where('team_id', Auth::user()->team_id);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class)
                    ->using(GroupPerson::class)
                     ->withTimestamps()
                     ->withPivot('notes', 'position', 'title', 'group_email', 'created_by', 'updated_by');
    }

    public function getPeopleAddedOnDate($date_str)
    {
        $date = Carbon::parse($date_str);
        $people_ids = GroupPerson::where('group_id', $this->id)
                          ->whereDate('updated_at', $date)
                          ->pluck('person_id');
        $people = Person::whereIn('id', $people_ids)
                        ->pluck('full_name');
        return $people->implode("\n");
    }

    public function groupPerson()
    {
        return $this->hasMany(GroupPerson::class);
    }

    public function numPeopleInGroup()
    {
        return GroupPerson::where('group_id', $this->id)->count();
    }

    public function cat()
    {
        //For whatever reason "category()" did not work!
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function files()
    {
        return $this->belongsToMany(WorkFile::class, 'group_file', 'group_id', 'file_id')
                    ->whereNull('group_file.deleted_at')
                    ->orderBy('files.name');
    }
    public function getActivityIconsAttribute()
    {
        $icons = [];

        for ($i=0; $i<$this->files()->count(); $i++) {
            $icons[] = 'file';
        }

        return $icons;
    }

    public function getCatNameAttribute($value)
    {
        return $this->name;
    }

    // public function getPivotDataAttribute($value)
    // {
    //     return json_decode($this->pivot->data);
    // }

    public function positions()
    {
        return $this->groupPerson()->whereNotNull('position')->pluck('position')->unique();
    }
}
