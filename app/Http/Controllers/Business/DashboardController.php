<?php

namespace App\Http\Controllers\Business;

use App\Contact;
use App\Http\Controllers\Controller;
use App\Models\Business\SalesContact;
use App\Models\Business\SalesEntity;
use App\Models\Business\SalesGoal;
use App\Models\Business\SalesTeam;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $goals = [];
        $year = Carbon::now()->format('Y');

        // Calculate Year

        $amount = SalesGoal::where('user_id', Auth::user()->id)
                                ->where('team_id', Auth::user()->team->id)
                                ->where('year', $year)
                                ->sum('amount');

        $contacts = Contact::where('user_id', Auth::user()->id)
                           ->where('team_id', Auth::user()->team->id)
                           ->whereYear('date', $year)
                           ->pluck('id')
                           ->toArray();

        $sum = SalesContact::whereIn('contact_id', $contacts)->sum('amount_secured');

        $goals[] = ['type'          => '',
                    'period'        => $year,
                    'need'          => $amount,
                    'have'          => $sum,
                    'percentage'    => ($amount > 0) ? round($sum / $amount * 100) : 0, ];

        // Calculate Quarters

        foreach ([1, 2, 3, 4] as $qtr) {
            $amount = SalesGoal::where('user_id', Auth::user()->id)
                                    ->where('team_id', Auth::user()->team->id)
                                    ->where('year', $year)
                                    ->where('quarter', $qtr)
                                    ->sum('amount');

            $qtr_start = (($qtr - 1) * 3) + 1;
            $qtr_end = $qtr_start + 2;

            $contacts = Contact::where('user_id', Auth::user()->id)
                               ->where('team_id', Auth::user()->team->id)
                               ->whereYear('date', $year)
                               ->whereMonth('date', '>=', $qtr_start)
                               ->whereMonth('date', '<=', $qtr_end)
                               ->pluck('id')
                               ->toArray();

            $sum = SalesContact::whereIn('contact_id', $contacts)->sum('amount_secured');

            $goals[] = ['type'          => 'Q',
                        'period'        => $qtr,
                        'need'          => $amount,
                        'have'          => $sum,
                        'percentage'    => ($amount > 0) ? round($sum / $amount * 100) : 0, ];
        }

        // Sales Entities

        $sales_team_types = SalesTeam::where('team_id', Auth::user()->team->id)
                                     ->where('user_id', Auth::user()->id)
                                     ->pluck('type')
                                     ->unique()
                                     ->toArray();

        $sales_entities = SalesEntity::where('team_id', Auth::user()->team->id)
                                     ->where('user_id', Auth::user()->id)
                                     ->whereIn('type', $sales_team_types)
                                     ->where('client', '!=', true);

        $prospect_types = $sales_entities->get()->pluck('type')->unique()->toArray();

        $prospects = $sales_entities->get();

        $checkins = SalesEntity::where('next_check_in', '<=',
                                            Carbon::today()->addWeeks(2)->toDateString())
                               // ->where('next_check_in', '>=',
                                            // Carbon::today()->toDateString())
                               ->get();

        return view(Auth::user()->team->app_type.'.dashboard.main', compact(
                                                    'prospect_types',
                                                    'prospects',
                                                    'goals',
                                                    'checkins'
                                                  ));
    }
}
