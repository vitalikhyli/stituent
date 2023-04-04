<?php

namespace App;

use App\Contact;
use App\WorkCase;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalendarWeekViewModel extends Model
{
    public function __construct($date)
    {
        $days = [];

        $next = Carbon::parse($date)->startOfWeek(0); //Sunday

        $thedate = $next->toDateString();
        foreach (['sun', 'mon', 'tue', 'wed', 'thr', 'fri', 'sat'] as $keyday) {
            if ($thedate == $date->toDateString()) {
                $is_today = true;
            } else {
                $is_today = false;
            }

            $days[$keyday] = ['num_contacts' => Contact::where('team_id', Auth::user()->team->id)->where('date', $thedate)->count(), 'num_cases' => WorkCase::where('team_id', Auth::user()->team->id)->where('date', $thedate)->count(), 'today' => $is_today];

            $next = $next->addDay(1);
            $thedate = $next->toDateString();
        }

        $this->days = $days;
    }
}
