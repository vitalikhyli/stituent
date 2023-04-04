<?php

namespace App;

use App\Person;
use App\Traits\RecordSignature;
use App\User;
use App\SharedCase;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DateTimeInterface;


class WorkCase extends Model
{
    use HasFactory;
    use RecordSignature;
    use SoftDeletes;

    public $table = 'cases';

    protected $dates = ['date', 'created_at', 'updated_at'];
    protected $appends = ['resolved_date'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function getResolvedDateAttribute()
    {
        if ($this->resolved) {
            return $this->updated_at->format('n/j/Y');
        }
    }
    public function getAppTextAttribute()
    {
        $text = "";
        $text .= $this->people->implode('name', ', ');
        if ($text) {
            $text .= "\n";
        }
        if ($this->resolved) {
            if ($this->types_string) {
                $text .= $this->types_string."\n";
            }
            $text .= 'RESOLVED '.$this->resolved_date.": ".$this->closing_remarks;
        } else {
            $text .= ''.$this->types_string.substr($this->notes, 0, 100)."(".$this->assigned_to_user.")";
        }
        $text = remove_bs($text);
        return $text;
    }
    public function getDateReadableAttribute()
    {
        return $this->created_at->format('n/j/Y');
    }
    public function getMonthReadableAttribute()
    {
        return $this->created_at->format('F Y');
    }
    public function getLastActivityMonthAttribute()
    {
        return $this->lastActivityDate()->format('M Y');
    }
    public function getLastActivityDateReadableAttribute()
    {
        return $this->lastActivityDate()->format('n/j/Y');
    }
    public function getLastActivityDateSortAttribute()
    {
        return $this->lastActivityDate()->format('Y-m-d');
    }
    public function lastActivityDate()
    {
        $date = $this->updated_at;

        if ($last_note = $this->contacts()->latest()->first()) {
            $last_note_date = $last_note->updated_at;
            if ($last_note_date > $date) {
                $date = $last_note_date;
            }
        }
        return $date;
    }
    public function entities()
    {
      return $this->belongsToMany(Entity::class, 'entity_cases', 'case_id', 'entity_id');
    }
    public function organizations()
    {
      return $this->entities();
    }

    public function getNameAttribute()
    {
        return $this->subject;
    }
    public function getTypesStringAttribute()
    {
        $str = "";
        if ($this->type) {
            $str .= strtoupper($this->type);
        }
        if ($this->subtype) {
            $str .= '/'.strtoupper($this->subtype);
        }
        if ($str) {
            $str .= ' ';
        }
        return $str;
    }
    public function isShared()
    {
        
    }
    public function sharedCases()
    {
        return $this->hasMany(SharedCase::class, 'case_id');
    }

    public function scopeStaffOrPrivateAndMine($query)
    {
        // Admins can see all contacts
        if (Auth::user()->permissions->admin) {
            return $query;
        } 

        // Otherwise
        return $query->where(function ($q) {
                    $q->orwhere('cases.private', false);
                    $q->orwhere(function ($w) {
                        $w->where('cases.private', true);
                        $w->where('cases.user_id', Auth::user()->id);
                    });
                });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getLinkedPeopleConcatenatedAttribute()
    {
        if (! $this->people) {
            return 'None';
        }

        return implode(', ', $this->people->pluck('full_name')->toArray());
    }

    public function getShortenedSubjectAttribute()
    {
        if (strlen($this->subject) > 20) {
            return substr($this->subject, 0, 20).'...';
        } else {
            return $this->subject;
        }
    }

    public function scopeTeamOrPrivateAndMine($query)
    {
        if (Auth::user()->admin) {
            // Admins can see all cases
            return $query;
        }

        return $query->where(function ($q) {
            $q->orwhere('cases.private', false);
            $q->orwhere(function ($w) {
                $w->where('cases.private', true);
                $w->where('cases.user_id', Auth::user()->id);
            });
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'case_id', 'id');
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'case_person', 'case_id', 'person_id');
    }

    public function households()
    {
        return $this->belongsToMany(VotingHousehold::class, 'case_household', 'case_id', 'household_id');
    }

    public function files()
    {
        return $this->belongsToMany(WorkFile::class, 'case_file', 'case_id', 'file_id')
                    ->whereNull('case_file.deleted_at')
                    ->orderBy('files.name');
    }

    public function assignedTo()
    {
        return User::where('id', $this->user_id)->first();
    }
    public function getAssignedToUserAttribute()
    {
        if ($user = $this->assignedTo()) {
            return $user->name;
        }
        return 'nobody';
    }
    public function getActivityIconsAttribute()
    {
        $icons = [];
        for ($i=0; $i<$this->people()->count(); $i++) {
            $icons[] = 'constituent';
        }
        for ($i=0; $i<$this->entities()->count(); $i++) {
            $icons[] = 'org';
        }
        for ($i=0; $i<$this->contacts()->count(); $i++) {
            $icons[] = 'note';
        }
        for ($i=0; $i<$this->files()->count(); $i++) {
            $icons[] = 'file';
        }

        return $icons;
    }

    public function notAssignedTo()
    {
        return User::where('current_team_id', $this->team_id)
                     ->where('id', '<>', $this->user_id)
                     ->where('active', true)
                     ->orderBy('name')
                     ->get();
    }

    public function getStreetAttribute()
    {
        // gets street of first person
        $firstperson = $this->people()->first();
        if ($firstperson) {
            return $firstperson->street;
        }
    }

    //============================================================>
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('status', '!=', 'resolved');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeHeld($query)
    {
        return $query->where('status', 'held');
    }

    //============================================================>
    public function getResolvedAttribute()
    {
        return $this->status == 'resolved';
    }

    public function getHeldAttribute()
    {
        return $this->status == 'held';
    }

    public function getOpenAttribute()
    {
        return $this->status == 'open';
    }
}
