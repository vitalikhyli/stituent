<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SharedCase;
use Auth;
use App\WorkCase;

class SharedCasesController extends Controller
{
    public function index()
    {
    	$allcases = WorkCase::where('team_id', Auth::user()->team_id)
    						->TeamOrPrivateAndMine()
    						->get();
    	$all_ids = $allcases->pluck('id');
    	//dd($allcases);
    	$sharing = SharedCase::whereIn('case_id', $all_ids)
    						 ->where('team_id', Auth::user()->team_id)
    						 ->get()
    						 ->groupBy('case_id');

    	$shared_team = SharedCase::where('shared_type', 'team')
    							 ->where('shared_team_id', Auth::user()->team_id)
    						 	 ->get();

    	$shared_user = SharedCase::where('shared_type', 'user')
    							 ->where('shared_user_id', Auth::user()->id)
    						 	 ->get();

    	$shared = $shared_team->merge($shared_user)->groupBy('case_id');
    	//dd($sharing);

    	//dd($allcases);
    	return view('shared-features.shared-cases.index', compact('allcases', 'sharing', 'shared'));
    }
    public function enable()
    {
    	$team = Auth::user()->team;
    	$team->shared_cases = true;
    	$team->save();
    	return redirect()->back();
    }
}
