<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UserLog;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class UserLogsController extends Controller
{
    public function graphData($data, $x, $y)
    {
        $data_x = ($data->pluck($x))->toArray();
        $data_y = ($data->pluck($y))->toArray();

        $data = [];
        foreach ($data_x as $key => $date) {
            $data[$key] = ['x' => Carbon::parse($data_x[$key])->toDateString(),
                             'y' => $data_y[$key],
                            ];
            $running = $data_y[$key];
        }

        return json_encode($data);
    }

    public function totalClicks()
    {
        $userlogs = UserLog::select(
                                    DB::raw('COUNT(*) as thecount'),
                                    DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as day')
                                )
                            ->whereNull('type') // No Ajax or Livewire
                            ->groupby('day')
                            ->get();

        $graph_data = $this->graphData($userlogs, 'day', 'thecount');
        $graph_type = 'bar';
        $graph_label = 'Total Non-AJAX Clicks';

        $min_time = Carbon::now()->toDateString();
        $max_time = Carbon::now()->toDateString();

        return view('admin.userlogs.clicks', compact('userlogs', 'max_time', 'min_time', 'graph_data', 'graph_type', 'graph_label'));
    }

    public function averageTime()
    {
        $min_time = Carbon::now()->toDateString();
        $max_time = Carbon::now()->toDateString();

        $userlogs = UserLog::select('url', 'type',
                                    DB::raw('AVG(time) as avgtime'),
                                    DB::raw('COUNT(*) as thecount'))
                            ->whereDate('created_at', '>=', $min_time)
                            ->whereDate('created_at', '<=', $max_time)
                            ->groupBy('url', 'type')
                            ->orderBy('avgtime', 'desc')
                            ->get();

        return view('admin.userlogs.avg-time', compact('userlogs', 'max_time', 'min_time'));
    }

    public function averageTimeDates(Request $request)
    {
        $from_date = Carbon::parse(request('from_date'))->toDateString();
        $to_date = Carbon::parse(request('to_date'))->toDateString();
        $userlogs = UserLog::select('url', 'type',
                                    DB::raw('AVG(time) as avgtime'),
                                    DB::raw('COUNT(*) as thecount'))
                            ->whereDate('created_at', '>=', $from_date)
                            ->whereDate('created_at', '<=', $to_date)
                            ->groupBy('url', 'type')
                            ->orderBy('avgtime', 'desc')
                            ->get();

        $max_time = Carbon::parse(UserLog::max('created_at'))->toDateString();
        $min_time = Carbon::parse(UserLog::min('created_at'))->toDateString();

        return view('admin.userlogs.avg-time', compact('userlogs', 'from_date', 'to_date', 'max_time', 'min_time'));
    }
}
