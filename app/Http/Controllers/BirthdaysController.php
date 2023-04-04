<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Voter;
use Carbon\Carbon;

class BirthdaysController extends Controller
{
    public function people($app_type)
    {
    	$people = Auth::user()->team->people()
                                 ->whereRaw(
                                      'DATE_FORMAT(curdate() - INTERVAL 3 DAY, "%m-%d") <= DATE_FORMAT(dob, "%m-%d")
                                      AND DATE_FORMAT(CURRENT_TIMESTAMP + INTERVAL 4 DAY, "%m-%d") >= DATE_FORMAT(dob, "%m-%d")')
                                 ->orderByRaw('DATE_FORMAT(dob, "%m-%d"), dob DESC')
                                 ->get();

        return view('shared-features.birthdays.people', compact('people'));
    }
    public function voters($app_type)
    {
    	$date = Carbon::today()->format('Y-m-d'); // could work dynamic, i.e. pass in date
    	$year = Carbon::today()->format('Y');
    	//dd($year); 
    	$year_interval = 10;
    	$end_of_month = Carbon::parse($date)->endOfMonth();
    	$days_until_end_of_month = Carbon::parse($date)->diffInDays($end_of_month);
    	
    	$voters = Voter::selectRaw("
				    	id,full_name, full_address, dob, TIMESTAMPDIFF(YEAR, dob, ('$date' + INTERVAL + $days_until_end_of_month DAY)) AS age")
    				   ->whereRaw(" 
							DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT('$date', '%m-%d') 
							and DATE_FORMAT(dob, '%m-%d') <= DATE_FORMAT(('$date' + INTERVAL + $days_until_end_of_month DAY), '%m-%d')
							and MOD($year - YEAR(dob), 5) = 0
							and archived_at is null
							and dob < '2020-01-01'")
				->orderBy('dob')
                ->simplePaginate(500);
        return view('shared-features.birthdays.voters', compact('voters', 'year', 'date'));       
   }
}
