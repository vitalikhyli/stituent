<?php

namespace App\Traits;

use App\WorkCase;
use Auth;
use Carbon\Carbon;

trait CalendarTrait
{
    ////////////////////////////////////////////////////////////////////////////////////
    //
    // CAMPAIGN
    //

    public function getCampaignCalendarEventsJson($monthsback)
    {
        $dates = [];

        $dates = $this->addCollectionByDate(\App\Campaign::class,
                                            'campaigns',
                                            'election_day',
                                            $monthsback,
                                            $dates);

        $dates = $this->addCollectionByDate(\App\CampaignEvent::class,
                                            'events',
                                            'date',
                                            $monthsback,
                                            $dates);

        $dates = $this->addCollectionByDate(\App\Donation::class,
                                            'contributions',
                                            'date',
                                            $monthsback,
                                            $dates);

        $events = $this->consolidateEvents($dates);

        return collect($events)->toJson();
    }

    public function addCollectionByDate($model, $type, $date_field, $monthsback, $dates)
    {
        $collection = $model::where('team_id', Auth::user()->team->id)
                                    ->where($date_field, '>', Carbon::today()->subMonths($monthsback))
                                    ->select($date_field)
                                    ->orderBy($date_field, 'desc')
                                    ->get();

        $collection = $collection->each(function ($item) use ($date_field) {
            $item['date'] = Carbon::parse($item[$date_field])->toDateString();
        });

        foreach ($collection as $item) {
            if (isset($dates[$item->date][$type])) {
                $dates[$item->date][$type] += 1;
            } else {
                $dates[$item->date][$type] = 1;
            }
        }

        return $dates;
    }

    public function consolidateEvents($dates)
    {
        $events = [];
        foreach ($dates as $date => $details) {
            $event = [];
            $event['date'] = $date;
            foreach ($details as $type => $val) {
                $event[$type] = $val;
            }
            $events[] = $event;
        }

        return $events;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // OFFICE
    //

    public function getCalendarEventsJson($monthsback)
    {
        $dates = [];

        $contacts_by_date = Auth::user()->contacts()
                                    ->where('created_at', '>', Carbon::today()->subMonths($monthsback))
                                    ->select('date')
                                    ->orderBy('date', 'desc')
                                    ->groupBy('date')
                                    ->get();

        foreach ($contacts_by_date as $contact) {
            if (isset($dates[$contact->date->format('Y-m-d')]['contacts'])) {
                $dates[$contact->date->format('Y-m-d')]['contacts'] += 1;
            } else {
                $dates[$contact->date->format('Y-m-d')]['contacts'] = 1;
            }
        }

        $cases_by_date = WorkCase::select('date')
                                 ->where('team_id', Auth::user()->team->id)
                                 ->where('created_at', '>', Carbon::today()->subMonths($monthsback))
                                 ->orderBy('date')
                                 ->groupBy('date')
                                 ->get();

        foreach ($cases_by_date as $case) {
            if ($case->date) {
                if (isset($dates[$case->date->format('Y-m-d')]['cases'])) {
                    $dates[$case->date->format('Y-m-d')]['cases'] += 1;
                } else {
                    $dates[$case->date->format('Y-m-d')]['cases'] = 1;
                }
            }
        }

        $events = [];
        foreach ($dates as $date => $details) {
            $event = [];
            $event['date'] = $date;
            foreach ($details as $type => $val) {
                $event[$type] = $val;
            }
            $events[] = $event;
        }

        return collect($events)->toJson();
    }
}
