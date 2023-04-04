<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Traits\CalendarTrait;
use App\WorkCase;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    use CalendarTrait;

    public function eventsByDate($date)
    {
        // if (Auth::user()->team->app_type == 'campaign') {
        //     return view('shared-features.calendar.events-by-date', compact('date', 'contacts', 'cases'));
        // }

        $date = Carbon::parse($date);
        $contacts = Auth::user()->contacts()->whereDate('date', $date)->get();
        $cases = WorkCase::whereDate('date', $date)
                              ->where('team_id', Auth::user()->team->id)
                              ->get();

        return view('shared-features.calendar.events-by-date', compact('date', 'contacts', 'cases'));
    }

    public function updateEvents($monthsback)
    {

        // if (Auth::user()->team->app_type == 'campaign') {
        //     return $this->getCampaignCalendarEventsJson(48);
        // }

        return $this->getCalendarEventsJson($monthsback);
    }

    public function updateAllEvents()
    {
        // if (Auth::user()->team->app_type == 'campaign') {
        //     return $this->getCampaignCalendarEventsJson(48);
        // }

        return $this->getCalendarEventsJson(48);
    }
}
