<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Team;

class DataController extends Controller
{
    public function localData()
    {
    	$local = [];
    	try {
	    	$local['teamName'] = Team::find(session()->get('team_id'))->name;
	    	$local['userName'] = Auth::user()->name;
	    	$local['numUsers'] = "".Auth::user()->team->activeUsers()->count();
	    	$local['voterCnt'] = "".number_format(Auth::user()->team->constituents_count);
	    	$local['uniqueId'] = "".rand(10000, 99999);
	    } catch (\Exception $e) {
	    	$errors = ['error' => 'Could not format local data'];
	    	return json_encode($errors);
	    }
	    return json_encode($local);
    }
}
