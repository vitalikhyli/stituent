<?php

namespace App\Http\Controllers;

use App\WorkCase;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CaseReportController extends Controller
{
    public function getCases($count = null)
    {
    	$cases = WorkCase::StaffOrPrivateAndMine()->where('team_id', Auth::user()->team->id);

    	if (request('owner') == 'mine') {
    		$cases = $cases->where('user_id', Auth::user()->id);
    	}

		if (request('resolved_month')) {

			$month = Carbon::parse(request('resolved_month'));
			$cases->whereDate('updated_at', '>=', $month->startOfMonth())
				  ->whereDate('updated_at', '<=', $month->endOfMonth())
				  ->where('status', 'resolved');

		} else if (request('status')) {

	    	$cases->where('status', request('status'));

	    }

    	if (request('type')) {
    		$cases = $cases->where('type', request('type'));
    	}

		//dd($cases->get());

		$start = '2000-01-01';
		$end = date('Y-m-d');
		if (request('opened_month')) {
			$month = Carbon::parse(request('opened_month'));
			$start =(clone $month)->startOfMonth();
			$end = (clone $month)->endOfMonth();
		} else {
			if (request('custom_from_date')) {
				$start = Carbon::parse(request('custom_from_date'));
			
			}
			if (request('custom_to_date')) {
				$end = Carbon::parse(request('custom_to_date'));
			
			}
		}

		
		$cases = $cases->whereDate('date', '>=', $start)
				  	   ->whereDate('date', '<=', $end)
				  	   ->orderBy('date', 'desc');

		// dd($cases->get(), request()->input(), $start, $end);

		if ($count) return $cases->count();
		return $cases->get();

	}

    public function web($app_type)
    {
        $cases = $this->getCases();
        $show_notes = (request('show_notes')) ? true : false;

        return view('shared-features.cases.report', compact('cases', 'show_notes'));
    }

    public function webSerial($app_type, $json)
	{
    	$json = base64_decode($json);
    	$json = json_decode($json);

        $cases = WorkCase::where('team_id', Auth::user()->team->id)
        				 ->whereIn('id', $json->ids)
        				 ->orderByDesc('date')
        				 ->get();
        				 
        $show_notes = $json->notes;

        return view('shared-features.cases.report', compact('cases', 'show_notes'));
    }

    public function count($app_type)
    {
        return $this->getCases($count = true);
    }
}
