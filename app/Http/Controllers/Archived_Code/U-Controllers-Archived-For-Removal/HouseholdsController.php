<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use App\Household;
use App\VotingHousehold;

use DB;

class HouseholdsController extends Controller
{

    public function searchCasesHouseholds($case_id, $v=null)
    {
        $v = trim($v);
        $mode_all       = 1;
        $search_value   = $v;

       if (strlen($v) > 2) {

            $households = Household::select('full_address','id')
                                   ->where('full_address','like','%'.$v.'%')
                                   ->where('team_id',Auth::user()->team->id);

            $households = VotingHousehold::select(DB::raw('household as full_address'),'id')
                                         ->where('household','like','%'.$v.'%')
                                         ->whereNotIn('id',$households->pluck('id'))
                                         ->union($households);

            $households = $households->get();


        } elseif ($v == null) {

            return null;

        }

        //Remove people already selected
        $attached_households = DB::table('case_household')
                                 ->where('case_id',$case_id)
                                 ->get()
                                 ->pluck('household_id')
                                 ->toArray();

        $households = $households->whereNotIn('id',$attached_households);


        return view('u.cases.list-households', compact('households',
                                                        'mode_all',
                                                        'search_value'));
    }


    public function show($id)
    {
        $household = Household::find($id);

        if (!$household) {

            $household = VotingHousehold::find($id);

            $external = true;

        } else {

            $external = false;

        }

        $nearby_households = null;

        if ($household) {

            $component = explode('|',$household->id);
            $try = [];
            $distance = 3;
            for ($i = ($distance *-1); $i <= ($distance); $i++) {
                $try[] = $component[0].'|'.$component[1].'|'.$component[2].'|'.str_pad($component[3]+$i, 8, '0', STR_PAD_LEFT);
            }

            $nearby_households_1 = Household::select('id',
                DB::raw('0 as external'),
                DB::raw('full_address as full_address'),
                DB::raw('total_residents as total_residents'))
                ->where('id','<>',$household->id)
                ->where(function($query) use ($try){
                    foreach($try as $thetry) {
                        $query->orWhere('id','like','%'.$thetry.'%');
                    }
                });

            $nearby_households = VotingHousehold::select('id',
                DB::raw('1 as external'),
                DB::raw('household as full_address'),
                DB::raw('residents_count as total_residents'))
                ->where('id','<>',$household->id)
                ->where(function($query) use ($try){
                    foreach($try as $thetry) {
                        $query->orWhere('id','like','%'.$thetry.'%');
                    }
                })
                ->whereNotIn('id',$nearby_households_1->pluck('id'));

            $nearby_households = $nearby_households->union($nearby_households_1)
                                                   ->orderBy('full_address')
                                                   ->get();

        }

        return view('u.households.show', compact('household',
                                                             'external',
                                                            'nearby_households'));
    }

    public function search($v) {
        $households = Household::where('team_id',Auth::user()->team->id)
                               ->where(function($q) use ($v){
                                   $q->orWhere('full_address','like','%'.$v.'%');
                               })
                               ->orderBy('id')
                               ->get();

        return view('u.households.list-households', compact('households'));
    }

    public function searchAll($v) {

        $households = Household::select(DB::raw("0 as external"),
                                        DB::raw("households.id as id"),
                                        DB::raw("full_address as full_address"),
                                        DB::raw("households.residents as residents"),
                                        'total_residents'
                                        )
                    ->where(function($q) use ($v){
                        $q->orWhere('full_address','like','%'.$v.'%');
                    })
                    ->where('households.team_id',Auth::user()->team->id);

        $households = VotingHousehold::select(DB::raw("1 as external"),
                                            DB::raw("id as id"),
                                            DB::raw("household as full_address"),
                                            DB::raw("residents as residents"),
                                            DB::raw("residents_count as total_residents")
                                            )
                    ->whereNotIn('id',$households->pluck('id'))
                    ->where(function($q) use ($v){
                        $q->orWhere('household','like','%'.$v.'%');
                    })
                    ->union($households)
                    ->orderBy('id');

        $households = $households->get();

        $mode_all = 1;

        return view('u.households.list-households', compact('households','mode_all'));
    }

    public function index()
    {
        $voting_hh_tbl = session('team_households_table');

        $households = Household::select(DB::raw("0 as external"),
                                        DB::raw("households.id as id"),
                                        DB::raw("households.full_address as full_address"),
                                        DB::raw("households.residents as residents"),
                                        'total_residents'
                                        )
                               ->where('team_id',Auth::user()->team->id)
                               ->orderBy('id')
                               ->take(100)->get();

        return view('u.households.index', compact('households'));
    }

    public function indexAll()
    {
        $households = Household::select(DB::raw("0 as external"),
                                        DB::raw("households.id as id"),
                                        DB::raw("full_address as full_address"),
                                        DB::raw("households.residents as residents"),
                                        'total_residents'
                                        )
                    ->where('households.team_id',Auth::user()->team->id);

        $households = VotingHousehold::select(DB::raw("1 as external"),
                                            DB::raw("id as id"),
                                            DB::raw("household as full_address"),
                                            DB::raw("residents as residents"),
                                            DB::raw("residents_count as total_residents")
                                            )
                    ->whereNotIn('id',$households->pluck('id'))
                    ->union($households)
                    ->orderBy('id');

        $households = $households->take(100)->get();

        $mode_all = 1;

        return view('u.households.index', compact('households','mode_all'));
    }

}
