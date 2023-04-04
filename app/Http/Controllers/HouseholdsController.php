<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Person;
use App\Voter;
use Auth;
use DB;
use Illuminate\Http\Request;

class HouseholdsController extends Controller
{

    public function show($app_type, $id)
    {
        $hh = Person::find($id);

        $people = Person::where('team_id', Auth::user()->team->id)
                        ->where('is_household', false)
                        ->where('household_id', $hh->household_id)
                        ->get();

        $voters = Voter::where('household_id', $hh->household_id)
                       ->whereNotIn('id', $people->pluck('voter_id'))
                       ->get();

        $residents = $people->merge($voters);

        $all_hh = Person::where('team_id', Auth::user()->team->id)
                        ->where('is_household', true)
                        ->get();

        return view('shared-features.households.show', compact('hh', 'all_hh', 'residents'));
    }

    public function edit($app_type, $id)
    {
        $hh = Person::find($id);
        return view('shared-features.households.edit', compact('hh'));
    }

    public function update(Request $request, $app_type, $id, $close = null)
    {
        $hh = Person::find($id);

        if (!$hh) return;

        foreach(['number', 
                 'fraction', 
                 'street', 
                 'apt', 
                 'city', 
                 'state', 
                 'zip'] as $field) {

            $hh->{'address_'.$field} = request('address_'.$field);
        }

        $hh->save();

        if ($close) {
            return redirect('/'.$app_type.'/households/'.$hh->id);
        } else {
            return redirect('/'.$app_type.'/households/'.$hh->id.'/edit');
        }
    }

    public function singlePointMap($app_type, $id)
    {
        $hh = Person::find($id);
        
        $activity = collect([]);
        $activity['households'] = collect([]);
        $activity['households'][] = ['address' => $hh->full_address,
                                     'lat' => $hh->address_lat,
                                     'lng' => $hh->address_long,
                                     'color' => 'yellow'];

        $zoom = .025;

        $activity['bounds'] = ['max_lat' => $hh->address_lat + $zoom,
                               'min_lat' => $hh->address_lat - $zoom,
                               'max_lng' => $hh->address_long + $zoom,
                               'min_lng' => $hh->address_long - $zoom];

        return $activity->toJson();
    }
}
