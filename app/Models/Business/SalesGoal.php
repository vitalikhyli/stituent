<?php

namespace App\Models\Business;

use App\Contact;
use App\Models\Business\SalesContact;
use App\Models\Business\SalesEntity;
use App\Models\Business\SalesGoal;
use Auth;
use Illuminate\Database\Eloquent\Model;

class SalesGoal extends Model
{
    protected $table = 'sales_user_goals';

    public function getTotalMetAttribute()
    {
        $amount = self::where('user_id', Auth::user()->id)
                                ->where('team_id', Auth::user()->team->id)
                                ->where('year', $this->year)
                                ->where('quarter', $this->quarter)
                                ->sum('amount');

        $qtr_start = (($this->quarter - 1) * 3) + 1;
        $qtr_end = $qtr_start + 2;

        $contacts = Contact::where('user_id', Auth::user()->id)
                           ->where('team_id', Auth::user()->team->id)
                           ->whereYear('date', $this->year)
                           ->whereMonth('date', '>=', $qtr_start)
                           ->whereMonth('date', '<=', $qtr_end)
                           ->pluck('id')
                           ->toArray();

        return SalesContact::whereIn('contact_id', $contacts)->sum('amount_secured');
    }
}
