<?php

namespace App;

use App\Traits\ListBuilderTrait;

use App\CampaignListUser;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;


class CampaignList extends Model
{
    use ListBuilderTrait;
    use SoftDeletes;

    protected $casts = [
        'form'          => 'array',
        'mail_data'     => 'array',
        'cached_voters' => 'array',
    ];

    public function getScriptFormattedAttribute()
    {
        $divs = explode("\n\n", $this->script);
        foreach($divs as $key => $div) {
            $divs[$key] = '<div class="mb-4">'.$div.'</div>';
        }
        $script = implode('', $divs);

        $script = str_replace("\n", '<div class=""></div>', $script);

        return $script;
    }

    public function reservedByOthers($voter_id)
    {
        $check = CampaignListUser::where('list_id', $this->id)
                                 ->where('reserved', $voter_id)
                                 ->where('user_id', '<>', Auth::user()->id)
                                 ->first();

        if ($check && Carbon::parse($check->reserved_expires_at)->isPast()) {
            $check->reserved = null;
            $check->reserved_expires_at = null;
            $check->save();
            $check = null;
        }

        return ($check) ? true : false;
    }

    public function reservedByUser($voter_id)
    {
        $check = CampaignListUser::where('list_id', $this->id)
                                 ->where('reserved', $voter_id)
                                 ->where('user_id', Auth::user()->id)
                                 ->first();

        if ($check && Carbon::parse($check->reserved_expires_at)->isPast()) {
            $check->reserved = null;
            $check->reserved_expires_at = null;
            $check->save();
            $check = null;
        }

        return ($check) ? true : false;
    }

    public function reservedExpiresAt($voter_id)
    {
        $check = CampaignListUser::where('list_id', $this->id)
                                ->where('reserved', $voter_id)
                                ->where('user_id', Auth::user()->id)
                                ->first();

        return $check->reserved_expires_at;
    }


    public function assignmentInstance($user)
    {
        return \App\CampaignListUser::where('list_id', $this->id)
                                    ->where('user_id', $user->id)
                                    ->first();
    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id');
    }

    public function assignments()
    {
        return $this->hasMany(CampaignListUser::class, 'list_id');
    }

    public function count()
    {
        if (!$this->static_count) {
            try {
                //dd($this);
                $thislist_clean = self::find($this->id);
                $thislist_clean->static_count = $this->voters()->count();
                $thislist_clean->save();
            } catch (\Exception $e) {
                return null;
            }

            return $thislist_clean->static_count;
        }

        return $this->static_count;
    }

    public function doorsCount()
    {
        //$this->static_count_doors = null;
        if (! $this->static_count_doors) {
            try {
                //dd("Static doors");
                $thislist_clean = self::find($this->id);
                $thislist_clean->static_count_doors = $this->voters()
                                                      ->groupBy('household_id')
                                                      ->count();
                $thislist_clean->save();
            } catch (\Exception $e) {
                return null;
            }

            return $thislist_clean->static_count_doors;
        }

        return $this->static_count_doors;
    }

    public function voterParticipants()
    {
        return $this->voters()->whereIn(session('team_table').'.id', getParticipants()->pluck('voter_id'));
    }

    public function getVoterIds()
    {
        if (is_array($this->cached_voters) && $this->cache) {
            //dd("cached voters");
            return array_keys($this->cached_voters);
        } else {
            //dd("voterids issue");
            return $this->voters()->pluck(session('team_table').'.id');
        }
    }
    public function getVoterCountAttribute()
    {
        if (is_array($this->cached_voters) && count($this->cached_voters) > 0) {
            $voter_count = count($this->cached_voters);
        } else {
            $voter_count = $this->voters()->count();
        }
        $this->static_count = $voter_count;
        $this->save();
        return $voter_count;
    }

    public function voters()
    {
        //dd("Laz");
        //dd($this);
        //return $this->buildMainQuery($this->form);
        //dd("voters()");
        if ($this->cache) {
            if (is_array($this->cached_voters)) {
                if (count($this->cached_voters) < 5000) {
                    // saves no time on huge lists
                    //dd("static less than");

                    return Voter::whereIn('id', $this->getVoterIds());
                } else {
                    //dd("main query");
                    //dd("Laz3");
                    return $this->buildMainQuery($this->form);
                }
            }
            $voters = $this->cacheVoters();
        } else {
            //dd("Laz4");
            $voters = $this->buildMainQuery($this->form);
            //dd($voters->toSql());
        }

        return $voters;
    }

    public function cacheVoters()
    {
        $voters = $this->buildMainQuery($this->form);
        try {
            $vcount = $voters->count();
            if ($vcount < 100000) {

                $voter_ids = $voters->pluck(session('team_table').'.id');
                $cached_voters = [];
                foreach ($voter_ids as $voter_id) {
                    $cached_voters[$voter_id] = 1;
                }
                $this->cached_voters = $cached_voters;
                $this->save();
                $household_count = $voters->pluck(session('team_table').'.household_id')
                                          ->unique()
                                          ->count();
                $this->static_count_doors = $household_count;
                $this->static_count = count($cached_voters);
                $this->save();
            }
        } catch (\Exception $e) {
            if (!auth()->user()) {
            //dd("ERROR", $e->getMessage());
                echo "ERROR: ".$e->getMessage()."\n\n";
            }
        }
        return $voters;
    }
    public function cacheVotersWithoutSave()
    {
        $voters = $this->buildMainQuery($this->form);
        try {
            $vcount = $voters->count();
            if ($vcount < 100000) {

                $voter_ids = $voters->pluck(session('team_table').'.id');
                $cached_voters = [];
                foreach ($voter_ids as $voter_id) {
                    $cached_voters[$voter_id] = 1;
                }
                $this->cached_voters = $cached_voters;
            }
        } catch (\Exception $e) {
            //dd("ERROR", $e->getMessage());
        }
        return $voters;
    }
    public function save(array $options = [])
    {
        // Add code to determine dynamic (i.e. uses campaign data)
        if ($this->updated_at < Carbon::now()->subMinutes(5)) {
            $this->cacheVotersWithoutSave();
        }
        // Extra fields added in ListBuilderTrait
        $extra_fields = ['input', 'debug', 'num_selected', 'current_count'];
        foreach ($extra_fields as $extra_field) {
            unset($this->$extra_field);
        }


        parent::save($options);
    }
}
