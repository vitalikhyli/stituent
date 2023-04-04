<?php

namespace App;

use App\Traits\RecordSignature;
use App\User;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DateTimeInterface;


class Contact extends Model
{
    use HasFactory;
    use RecordSignature;
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'date'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function getNameAttribute()
    {
        if (strlen($this->subject) > 0) {
            return $this->subject;
        }
        if ($this->people()->count() > 0) {
            return $this->people()->first()->name;
        }
        if ($this->case) {
            return $this->case->name;
        }
        if ($this->entities()->count() > 0) {
            return $this->entities()->first()->name;
        }
        return '';
    }
    public function getEmailsAttribute()
    {
        $string = $this->subject.' '.$this->notes;
        $pattern = emailRegexPattern();
        preg_match_all($pattern, $string, $matches);

        if (isset($matches) && isset($matches[0])) {
            return collect($matches[0]);
        }

        return [];
    }
    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class);
    }
    public function contactEntities()
    {
        return $this->hasMany(ContactEntity::class);
    }

    public function getNotesRegexAttribute()
    {
        $notes = $this->notes;

        // Emails
        $pattern = '/'.'[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}'.'/i';
        preg_match_all($pattern, $notes, $matches);
        if (isset($matches[0])) {
            $matches = collect($matches[0])->unique();
            $tag_a = '<span class="rounded-full px-2 whitespace-no-wrap border bg-blue-lightest">';
            $tag_b = '</span>';
            foreach ($matches as $match) {
                $notes = str_replace($match, $tag_a.$match.$tag_b, $notes);
            }
        }

        // Return formatted
        $pattern = '/'
            .'(\([0-9]{3}\)(\s|\.|-)?|[0-9]{3}(\s|\.|-))[0-9]{3}(\s|\.|-)[0-9]{4}'
            .'/';
        preg_match_all($pattern, $notes, $matches);
        if (isset($matches[0])) {
            $matches = collect($matches[0])->unique();
            $tag_a = '<span class="rounded-full px-2 whitespace-no-wrap border bg-orange-lightest">';
            $tag_b = '</span>';
            foreach ($matches as $match) {
                $notes = str_replace($match, $tag_a.$match.$tag_b, $notes);
            }
        }

        return $notes;
    }

    public function salescontact()
    {
        return $this->hasOne(\App\Models\Business\SalesContact::class);
    }

    public function getFollowupTextAttribute()
    {
        $str = "";
        if ($this->followup) {
            $str .= "Requires Followup ";
            if ($this->followup_on) {
                $str .= $this->followup_on.' ';
            }
            if ($this->followup_done) {
                $str .= '- Done';
            }
            
        }
        return trim($str);
    }
    public function getRequiresFollowupAttribute()
    {
        if ($this->followup && !$this->followup_done) {
            return true;
        }
        return false;
    }

    public function getLinkedPeopleConcatenatedAttribute()
    {
        if (! $this->people) {
            return 'None';
        }

        return implode(', ', $this->people->pluck('full_name')->toArray());
    }

    public function scopeTeamOrPrivateAndMine($query)
    {

        // Admins can see all contacts, even private, on same team
        if (Auth::user()->permissions->admin) {
            return $query->where(function ($q) {
                $q->orwhere('contacts.private', false);
                $q->orwhere(function ($w) {
                    $w->where('contacts.private', true);
                    $w->where('contacts.team_id', Auth::user()->team_id);
                });
            });
        }

        // Otherwise
        return $query->where(function ($q) {
            $q->orwhere('contacts.private', false);
            $q->orwhere(function ($w) {
                $w->where('contacts.private', true);
                $w->where('contacts.user_id', Auth::user()->id);
            });
        });
    }

    public function getNotesAttribute($notes)
    {
        $search = ['â€“', 'â€”', 'â€˜', 'â€™', 'â€œ', 'â€', 'â€¦'];
        $replace = ['—',  '–',  '‘',  '’',  '“',  '”',  '...'];
        $notes = preg_replace("/[\r\n]+/", "\n", $notes);

        return trim(str_replace($search, $replace, $notes));
    }

    public function getActivityIconsAttribute()
    {
        $icons = [];
        if ($this->case) {
            $icons[] = 'case';
        }
        for ($i=0; $i<$this->people()->count(); $i++) {
            $icons[] = 'constituent';
        }
        for ($i=0; $i<$this->entities()->count(); $i++) {
            $icons[] = 'org';
        }

        return $icons;
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'contact_person', 'contact_id')->withTimestamps();
    }

    public function entities()
    {
        return $this->belongsToMany(Entity::class, 'contact_entity', 'contact_id', 'entity_id')->withTimestamps();
    }

    public function case()
    {
        return $this->belongsTo(WorkCase::class);
    }

    public function getCallLogAttribute()
    {
        return $this->source == 'call_log';
    }

    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'desc')->limit(1)->first();
    }

    public function assignedTo()
    {
        return User::where('id', $this->user_id)->first();
    }

    public function getDateCleanAttribute()
    {
        return $this->date->format('Y-m-d');
    }
    public function getDateReadableAttribute()
    {
        return $this->date->format('n/j/Y');
    }
    public function getMonthReadableAttribute()
    {
        return $this->date->format('F Y');
    }
    public function getDateTimeCleanAttribute()
    {
        if ($this->date) {
            return $this->date->format('Y-m-d H:i:s');
        }
    }

    public function getStreetAttribute()
    {
        // gets street of first person
        $firstperson = $this->people()->first();
        if ($firstperson) {
            return $firstperson->street;
        }
    }

    public function getUserNameAttribute()
    {
        return User::find($this->user_id)->name;
    }
}
