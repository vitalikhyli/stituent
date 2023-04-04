<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Team;
use App\User;
use App\Account;
use App\UserLog;
use DB;
use Carbon\Carbon;

class PhoneAppController extends Controller
{
    public function index()
    {
    	$paying_accounts = Account::where('billygoat_id', '>', 0)
    							  ->pluck('id');
    	$paying_accounts[] = 1;

    	$office_teams = Team::with('users.devices')
    						->whereIn('account_id', $paying_accounts)
    					    ->where(function($subquery) {
                                return $subquery->where('app_type', 'office')
                                               ->orWhere('app_type', 'u');
                                })
    						->where('active', true)
    						->get()
    						->sortBy('name');

    						//dd($office_teams);

    	$used_in_last_year = UserLog::select('user_id', DB::raw('count(*) as clicks'))
    								// ->whereNull('type')
    								// ->where('created_at', '>', Carbon::now()->subYear())
    								->groupBy('user_id')
    								->get();
    	$last_year_lookup = [];
    	foreach ($used_in_last_year as $userlog) {
    		$last_year_lookup[$userlog->user_id] = $userlog->clicks;
    	}
    	//dd($last_year_lookup);

    	return view('admin.phone-app.index', compact('office_teams', 'last_year_lookup'));
    }

    public function store()
    {
    	$user_id = request('user_id');
    	$user = User::find($user_id);
    	$user->current_app_pin;
    	return redirect(url()->previous().'#user_'.$user_id);
    }
}
