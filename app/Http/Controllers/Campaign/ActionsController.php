<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Action;
use App\Participant;

use App\Traits\ExportTrait;


class ActionsController extends Controller
{
    use ExportTrait;

    public function index()
    {
    	//dd("Laz");
    	//dd(Action::all());
    	$actions = Auth::user()->team
    				   ->actions()
    				   ->latest()
    				   ->get();
    	//dd($actions);
    	return view('campaign.actions.index', compact('actions'));
    }

    public function regulars()
    {
    	$actions = Auth::user()->team->actions()
    						         ->get();
    	$grouped = $actions->groupBy('participant_id');
    	
    	$grouped_sorted = $grouped->sortByDesc(function ($actions, $pid) {
		    return count($actions);
		});

    	return view('campaign.actions.regulars', compact('grouped_sorted'));
    }

    public function export()
    {
       $actions = Action::select('actions.created_at',
                                 'first_name',
                                 'last_name',
                                 'actions.name as what',
                                 'details',
                                 'users.name as by',
                                 'participants.id as participant_id')
                ->leftJoin('participants', 'actions.participant_id', 'participants.id')
                ->leftJoin('users', 'actions.user_id', 'users.id')
                ->where('actions.team_id', Auth::user()->team->id)
                ->latest()
                ->get()
                ->each(function ($i) {
                    $participant = Participant::find($i['participant_id']);
                    $i['current_support'] = (!$participant) ? null : $participant->support;
                    unset($i['participant_id']);
                });

        return $this->createCSV($actions);
    }
}
