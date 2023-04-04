<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\SalesEntity;
use App\Models\Business\SalesTeam;
use Auth;
use Illuminate\Http\Request;

class SalesTeamsController extends Controller
{
    public function index()
    {
        $types_all = SalesTeam::where('team_id', Auth::user()->team->id)
                          ->get()
                          ->pluck('type')
                          ->unique();

        $types = SalesEntity::where('team_id', Auth::user()->team->id)
                                 ->get()
                                 ->pluck('type')
                                 ->unique();

        // Remove orphanied SalesTeams (when no SalesEntity has such a type)
        $ophaned_types = $types->diff($types_all)->unique();
        SalesTeam::whereIn('type', $ophaned_types->toArray())->delete();

        $salesteams = SalesTeam::where('team_id', Auth::user()->team->id)->get();

        return view(Auth::user()->team->app_type.'.salesteam.index', compact(
                                                    'types',
                                                    'salesteams'
                                                  ));
    }

    public function update(Request $request)
    {
        foreach ($request->input() as $field => $value) {
            $field = explode('_', $field);

            if ($field[0] == 'add') {
                $user_id = $field[1] * 1;
                $type = base64_decode($field[2]);

                $pivot = SalesTeam::where('user_id', $user_id)->where('type', $type)->first();

                if (! $pivot) {
                    $pivot = new SalesTeam;
                }

                $pivot->team_id = Auth::user()->team->id;
                $pivot->user_id = $user_id;
                $pivot->type = $type;
                $pivot->save();
            }

            if ($field[0] == 'remove') {
                $user_id = $field[1] * 1;
                $type = base64_decode($field[2]);

                if (request('add_'.$user_id.'_'.base64_encode($type))) {
                    continue;
                }

                $pivot = SalesTeam::where('user_id', $user_id)->where('type', $type)->delete();
            }
        }

        return back();
    }
}
