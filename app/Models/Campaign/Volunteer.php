<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

use App\User;


class Volunteer extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $casts = ['notified_at' => 'array',
                        'types'       => 'array'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {

            $model->notified_at = [];
            $model->types       = [];

            if (!$model->uuid) {
                $check = true;
                while ($check == true) {
                    $uuid = (string) Str::uuid();
                    if (!Volunteer::where('uuid', $uuid)->exists()) $check = false;
                }

                $model->uuid = $uuid;
                $model->uuid_expires_at = Carbon::now()->addWeeks(4);
            }

        });

    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function opportunities()
    {
        return $this->belongsToMany(Opportunity::class);
    }

    public function recordNotication()
    {
        $history = $this->notified_at;
        $history[] = Carbon::now()->toDateTimeString();
        $this->notified_at = $history;
        $this->save();
    }

    public function regenerateUUID()
    {
        $check = true;
        while ($check == true) {
            $uuid = (string) Str::uuid();
            if (!Guest::where('uuid', $uuid)->exists()) $check = false;
        }

        $this->uuid = $uuid;
        $this->uuid_expires_at = Carbon::now()->addWeeks(4);
        $this->save();
    }

    public function createSession()
    {
        // Start Session
    	session(['guest' => $this->id]);

        // Record Login
        $this->last_login = Carbon::now();
        $this->save();

        // Login the CF User with Auth too
        if ($this->user_id) {
            $user = User::find($this->user_id);
            if ($user) {
                Auth::loginUsingID($user->id);
            }
        }
    }

    public function endSession()
    {
        session()->forget('guest');
    }

    public function createNewFromUser($user)
    {
        $this->email       = Auth::user()->email;
        $this->user_id     = Auth::user()->id;
        $this->team_id     = Auth::user()->team->id;
        $this->username    = Auth::user()->username;
    }

}
