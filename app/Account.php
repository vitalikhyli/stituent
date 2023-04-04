<?php

namespace App;

use App\Team;
use App\TeamUser;
use App\Traits\RandomWordsTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Str;

class Account extends Model
{
    use RandomWordsTrait;

    protected $casts = ['billygoat_data' => 'array'];

    public function hasTeamType($type)
    {
        return $this->teams->pluck('app_type')->contains($type);
    }

    public function users()
    {
        $team_ids = Team::where('account_id', $this->id)->pluck('id')->toArray();
        $user_ids = TeamUser::whereIn('team_id', $team_ids)->pluck('user_id')->toArray();
        $users = User::whereIn('id', $user_ids)->get();

        return $users;
    }

    public function save(array $options = [])
    {
        if (! $this->uuid) {
            $this->uuid = Str::uuid();
        }
        if (! $this->payment_simple) {
            $this->payment_simple = $this->randomNoun(6).'-'.$this->randomNoun(6);
        }

        return parent::save($options);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function getClicksAttribute()
    {
        $uls = UserLog::whereIn('team_id', $this->teams->pluck('id'))
                      ->whereNull('type')
                      ->whereNull('mock_id');
        //dd($uls->toSql());
        return $uls->count();
    }

    public function hasRequiredInfo()
    {
        if (
            ($this->name) &&
            ($this->contact_name) &&
            ($this->address) &&
            ($this->city) &&
            ($this->state) &&
            ($this->zip) &&
            ($this->email)
            ) {
            return true;
        } else {
            return false;
        }
    }

    public function getPaidThroughDateAttribute()
    {
        if (isset($this->billygoat_data['paid_through_date'])) {
            return Carbon::parse($this->billygoat_data['paid_through_date']);
        }
    }

    public function getAnnualPriceAttribute()
    {
        $first_team = $this->teams()->first();
        $type = $first_team->district_type;
        switch ($type) {
            case 'S':
                return 2400;
            case 'H':
                return 1200;
        }

        return null;
    }

    public function billyGoatOutstandingBal($formatted = null)
    {
        if (! $this->billygoat_id) {
            return 'No account';
        }

        if (config('app.env') == 'local') {
            return 66000;
            $domain = config('app.billygoat_local');
        } else {
            $domain = config('app.billygoat_url');
        }

        $url = $domain.'/api/'.config('app.billygoat_api_key').'/'.$this->billygoat_id.'/outstandingbalance';

        //Better way to do this????

        $response = @file_get_contents($url);

        if ($response === false) {
            return 'Error';
        } else {
            $outstanding_balance = $response;
        }

        if ($formatted) {
            if (is_numeric($outstanding_balance)) {
                $outstanding_balance = $outstanding_balance / 100; //Pennies
                $outstanding_balance = '$ '.number_format($outstanding_balance, 2, '.', ',');
            }
        }

        return $outstanding_balance;
    }
}
