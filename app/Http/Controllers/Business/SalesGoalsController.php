<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\SalesGoal;
use Auth;
use Illuminate\Http\Request;

class SalesGoalsController extends Controller
{
    public function index()
    {
        $goals = SalesGoal::where('team_id', Auth::user()->team->id)
                          ->where('user_id', Auth::user()->id)
                          ->orderBy('year')
                          ->orderBy('quarter')
                          ->get();

        return view(Auth::user()->team->app_type.'.goals.index', compact(
                                                    'goals'
                                                  ));
    }

    public function save(Request $request)
    {
        $goal = new SalesGoal;
        $goal->team_id = Auth::user()->team->id;
        $goal->user_id = Auth::user()->id;
        $goal->year = request('year');
        $goal->quarter = request('quarter');
        $goal->amount = request('amount');
        $goal->save();

        return redirect()->back();
    }

    public function delete($id)
    {
        $goal = SalesGoal::find($id);
        $goal->delete();

        return back();
    }
}
