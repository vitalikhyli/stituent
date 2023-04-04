<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;


class Opportunity extends Model
{
    use HasFactory;

    protected $casts = ['starts_at' => 'date',
						'ends_at' => 'date',
                        'schedule' => 'object'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $model->matrix = json_encode([]);
        });

        static::saving(function($model) {

            //

        });

    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }


    public function scopeActiveAndNotExpired($query)
    {
        if ($this->ends_at) {
            return $query->where('active', true)->where('ends_at', '>', Carbon::now());
        } else {
            return $query->where('active', true);
        }
    }



    public function scopeThisCampaign($query)
    {
        return $query->where('campaign_id', CurrentCampaign()->id);
    }

    public function list()
    {
        return $this->belongsTo(\App\CampaignList::class);
    }

    public function invited()
    {
        return $this->belongsToMany(Volunteer::class)->withPivot(['emailed_at']);
    }

}
