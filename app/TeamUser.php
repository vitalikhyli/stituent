<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

// class TeamUser extends Model
class TeamUser extends Pivot
{
    protected $table = 'team_user';

    public $incrementing = false;

    public function getBgColorAttribute()
    {
        // if ($this->bg_color) return 'bg-'.$this->bg_color;
        // if ($this->team->bg_color) return 'bg-'.$this->team->bg_color;
        return 'bg-'.'orange';
    }

    public function getLookupColorAttribute()
    {
        return 'bg-transparent border-white';

        $bg = $this->bg_color;
        $bg_array = explode('-', $bg);
        $color = $bg_array[1];
        $bg_suffix = (isset($bg_array[2])) ? $bg_array[2] : null;

        if ($bg_suffix == 'lightest') {
            $suffix = '-lighter';
        }
        if ($bg_suffix == 'lighter') {
            $suffix = '-light';
        }
        if ($bg_suffix == 'light') {
            $suffix = null;
        }
        if ($bg_suffix == null) {
            $suffix = '-dark';
        }
        if ($bg_suffix == 'dark') {
            $suffix = '-darker';
        }
        if ($bg_suffix == 'darker') {
            $suffix = '-darkest';
        }
        if ($bg_suffix == 'darkest') {
            return 'bg-black';
        }

        return 'bg-'.$color.$suffix;
    }

    public function team()
    {
        return $this->belongsTo(Team::class, $this->current_team_id);
    }
}
