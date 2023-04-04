<?php

namespace App\Http\Controllers;

use App\Person;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    //====================================================================================>
    //
    // CHART.JS DOCUMENTATION:
    // https://www.chartjs.org/docs/latest/charts/line.html#stepped-line
    //
    //

    public function engagementTest()
    {
        $team = \App\Team::where('name', 'All Campaigns')->first();

        $chart_data = $team->people()->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-01') as monthyear"))
               ->groupby('monthyear')
               ->orderBy('monthyear')
               ->get();

        $data_x = ($chart_data->pluck('monthyear'))->toArray();
        $data_y = ($chart_data->pluck('data'))->toArray();

        $data = [];
        $running = 0;
        foreach ($data_x as $key => $date) {
            $data[$key] = ['x' => Carbon::parse($data_x[$key])->format('Y-m-01'),
                             'y' => $data_y[$key] + $running,
                            ];
            $running = $data_y[$key] + $running;
        }

        // return json_encode($data);

        $data = json_encode($data); //$this->graphDataForPeople();

        $label = 'Constituents';

        $type = 'line';

        return view('shared-features.metrics.engagement-test', compact('type', 'data', 'label'));
    }

    public function cases()
    {
        $data = $this->graphDataForCases();
        $label = 'All Cases';

        $data_2 = $this->graphDataForCases($status = 'resolved');
        $label_2 = 'Resolved Cases';

        $type = 'line';

        return view('shared-features.metrics.engagement', compact('type',
                                                                  'data', 'label',
                                                                  'data_2', 'label_2'));
    }

    public function contacts()
    {
        $data = $this->graphDataForContacts();
        $label = 'Contacts';

        $type = 'bar';

        return view('shared-features.metrics.engagement', compact('type', 'data', 'label'));
    }

    public function graphDataForContacts()
    {
        $chart_data = Auth::user()->team->contacts()->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-01') as monthyear"))
               ->groupby('monthyear')
               ->orderBy('monthyear')
               ->get();

        $data_x = ($chart_data->pluck('monthyear'))->toArray();
        $data_y = ($chart_data->pluck('data'))->toArray();

        $data = [];
        foreach ($data_x as $key => $date) {
            $data[$key] = ['x' => Carbon::parse($data_x[$key])->format('Y-m-01'),
                             'y' => $data_y[$key],
                            ];
        }

        return json_encode($data);
    }

    public function graphDataForPeople()
    {
        $chart_data = Auth::user()->team->people()->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-01') as monthyear"))
               ->groupby('monthyear')
               ->orderBy('monthyear')
               ->get();

        $data_x = ($chart_data->pluck('monthyear'))->toArray();
        $data_y = ($chart_data->pluck('data'))->toArray();

        $data = [];
        $running = 0;
        foreach ($data_x as $key => $date) {
            $data[$key] = ['x' => Carbon::parse($data_x[$key])->format('Y-m-01'),
                             'y' => $data_y[$key] + $running,
                            ];
            $running = $data_y[$key] + $running;
        }

        return json_encode($data);
    }

    public function graphDataForCases($status = null)
    {
        if ($status == 'resolved') {
            $chart_data = Auth::user()->team->cases()
                              ->resolved()
                              ->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-01') as monthyear"))
                              ->groupby('monthyear')
                              ->orderBy('monthyear')
                              ->get();
        } else {
            $chart_data = Auth::user()->team->cases()
                              ->select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-01') as monthyear"))
                              ->groupby('monthyear')
                              ->orderBy('monthyear')
                              ->get();
        }

        $data_x = ($chart_data->pluck('monthyear'))->toArray();
        $data_y = ($chart_data->pluck('data'))->toArray();

        $data = [];
        $running = 0;
        foreach ($data_x as $key => $date) {
            $data[$key] = ['x' => Carbon::parse($data_x[$key])->format('Y-m-01'),
                             'y' => $data_y[$key] + $running,
                            ];
            $running = $data_y[$key] + $running;
        }

        return json_encode($data);
    }

    // public function engagement()
    // {
    //     $data = $this->graphDataForPeople();
    //     $label = 'Constituents';

    //     $type = 'line';

    //     return view('shared-features.metrics.engagement', compact('type', 'data', 'label'));
    // }

    public function engagement()
    {
        $chart_data = [];
        $chart_data['max'] = Auth::user()->team->people()->count();

        $start_date = Carbon::parse(Auth::user()->team->people()->min('created_at'));
        $end_date = Carbon::today();
        $days_between = $end_date->diffInDays($start_date);

        $max_bars = 44;
        $days_per_bar = $days_between / $max_bars;

        $bars = [];
        $count = 0;
        $labels_every = 5;
        for ($d = $start_date; $d <= $end_date; $d->addDays($days_per_bar)) {
            $onebar = [];
            $onebar['value'] = Auth::user()->team->people()->where('created_at', '<=', $d)->count();
            if ($count % $labels_every == 0) {
                $onebar['label'] = $d->format('M Y');
            } else {
                $onebar['label'] = '';
            }
            $bars[] = $onebar;
            $count++;
        }
        $lastbar = [];
        $lastbar['value'] = $chart_data['max'];
        $lastbar['label'] = 'TODAY';
        $bars[] = $lastbar;

        //dd($bars);
        $chart_data['bars'] = $bars;
        //dd($chart_data);

        //dd($days_between);

        return view('shared-features.metrics.engagement-original', compact('chart_data'));
    }
}
